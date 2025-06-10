<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Face;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\TokenRefreshService;

class AutoCheckIn extends Command
{
    protected $signature = 'absen:auto-checkin';
    protected $description = 'Melakukan checkin otomatis untuk user dengan absent_type 1';

    public function handle() {
        $users = User::where('absent_type', 1)->get();
        
        $this->info("Memproses {$users->count()} users untuk check-in otomatis");

        $successCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            try {
                $this->info("Memproses user ID: {$user->id} - {$user->name}");

                // Cek apakah akun sudah expired
                if ($user->expired && $user->expired < now()->format('Y-m-d')) {
                    $this->error("User ID {$user->id} akun sudah expired pada {$user->expired}");
                    $this->sendTelegramNotification($user, 'Check-in gagal: Akun sudah expired, hubungi admin');
                    $failedCount++;
                    continue;
                }
                
                if ($this->checkinUser($user)) {
                    $successCount++;
                    $this->info("✓ User ID {$user->id} berhasil check-in");
                } else {
                    $failedCount++;
                    $this->error("✗ User ID {$user->id} gagal check-in");
                }
                
                // Tambahkan delay untuk menghindari rate limiting
                sleep(2);
                
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Exception untuk user ID {$user->id}: " . $e->getMessage());
                Log::error("AutoCheckIn Exception", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Proses check-in selesai. Berhasil: {$successCount}, Gagal: {$failedCount}");
    }

    private function sendTelegramNotification($user, $message) {
        if (!$user->telegram_id) {
            return;
        }

        $payload = [
            'message' => $message,
            'to' => [(int) $user->telegram_id]
        ];

        try {
            $response = Http::timeout(10)->post('https://telebot.saijaan.com/send', $payload);
            if (!$response->successful()) {
                Log::warning('Gagal mengirim notifikasi Telegram', [
                    'user_id' => $user->id,
                    'telegram_id' => $user->telegram_id,
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim notifikasi Telegram', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function checkinUser($user) {
        // 1. PASTIKAN TOKEN VALID (refresh jika perlu)
        if (!TokenRefreshService::ensureValidToken($user)) {
            $this->error("Gagal memastikan token valid untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Token tidak dapat di-refresh, mungkin refresh token sudah expired');
            return false;
        }

        // Reload user untuk mendapat token terbaru
        $user->refresh();

        // 2. Validasi data user
        if (!$user->api_token) {
            $this->error("Token tidak tersedia untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Token tidak tersedia');
            return false;
        }

        if (!$user->latitude || !$user->longitude) {
            $this->error("Koordinat tidak tersedia untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Koordinat tidak tersedia');
            return false;
        }

        // 3. Validasi gambar wajah
        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $user->id)
            ->where('day', $currentDay)
            ->get();

        if ($faceImages->isEmpty()) {
            $this->error("Tidak ada gambar wajah tersedia untuk user ID {$user->id} pada hari {$currentDay}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Tidak ada gambar wajah tersedia untuk hari ini');
            return false;
        }

        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        if (!Storage::disk('private')->exists("face/{$fileName}")) {
            $this->error("File tidak ditemukan: face/{$fileName} untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: File foto tidak ditemukan');
            return false;
        }

        $filePath = Storage::disk('private')->path("face/{$fileName}");

        // Pastikan file dapat dibaca
        if (!is_readable($filePath)) {
            $this->error("File tidak dapat dibaca: {$filePath} untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: File foto tidak dapat dibaca');
            return false;
        }

        // 4. Generate koordinat random
        [$randomLat, $randomLong] = $this->generateRandomCoordinates(
            $user->latitude, $user->longitude, $user->radius ?? 100
        );

        // 5. Prepare GraphQL mutation
        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat"    => $randomLat,
                    "long"   => $randomLong,
                    "tipe"   => "in",
                    "status" => "dalam",
                    "foto"   => null,
                ]
            ],
            "query" => "mutation CreatePresence(\$createPresensiInput: CreatePresensiInput!) {
                createPresensi(createPresensiInput: \$createPresensiInput) {
                    presensi_id
                    employee_nip
                    presensi_tipe
                    presensi_date
                    presensi_time
                    presensi_lat
                    presensi_long
                    presensi_status
                    presensi_foto_url
                    presensi_foto_file_name
                    presensi_sync_eabsen
                    presensi_sync_eabsen_id
                    __typename
                }
                __typename
            }"
        ]);

        // 6. Send API request
        try {
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                throw new \Exception("Tidak dapat membuka file: {$filePath}");
            }

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->asMultipart()
                ->withHeaders([
                    'apollo-require-preflight' => 'true',
                    'Authorization'            => 'Bearer ' . $user->api_token,
                    'User-Agent'              => 'Laravel-AutoCheckIn/1.0',
                ])
                ->attach('file', $fileHandle, $fileName)
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }

            $status = $response->status();
            $body = $response->body();

            // Log response untuk debugging
            Log::info("CheckIn API Response", [
                'user_id' => $user->id,
                'status' => $status,
                'body' => substr($body, 0, 500) // Limit log size
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Periksa apakah response mengandung error GraphQL
                if (isset($responseData['errors']) && !empty($responseData['errors'])) {
                    $errorMessage = $responseData['errors'][0]['message'] ?? 'GraphQL Error';
                    
                    // Jika error karena token, coba refresh sekali lagi
                    if (str_contains(strtolower($errorMessage), 'unauthorized') || 
                        str_contains(strtolower($errorMessage), 'token')) {
                        
                        $this->info("Token error detected, trying to refresh token for user {$user->id}");
                        
                        if (TokenRefreshService::refreshUserToken($user)) {
                            $user->refresh();
                            $this->info("Token refreshed, retrying check-in for user {$user->id}");
                            // Recursive call dengan token baru (maksimal 1 kali retry)
                            return $this->checkinUserWithFreshToken($user, $filePath, $fileName, $randomLat, $randomLong);
                        }
                    }
                    
                    $this->error("User ID {$user->id} GraphQL Error: {$errorMessage}");
                    $this->sendTelegramNotification($user, "Check-in gagal: {$errorMessage}");
                    return false;
                }

                $this->info("User ID {$user->id} check-in berhasil.");
                $this->sendTelegramNotification($user, 'Check-in otomatis berhasil untuk ' . $user->name);
                return true;
            } else {
                $this->error("User ID {$user->id} check-in gagal. Status: {$status}, Body: {$body}");
                $this->sendTelegramNotification($user, "Check-in otomatis gagal: HTTP {$status}");
                return false;
            }

        } catch (\Exception $e) {
            $this->error("Exception untuk user ID {$user->id}: " . $e->getMessage());
            $this->sendTelegramNotification($user, "Check-in gagal: " . $e->getMessage());
            
            Log::error("CheckIn Exception", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'file' => $fileName,
                'coordinates' => [$randomLat, $randomLong]
            ]);
            
            return false;
        }
    }

    /**
     * Retry check-in dengan token yang sudah di-refresh (maksimal 1 kali)
     */
    private function checkinUserWithFreshToken($user, $filePath, $fileName, $randomLat, $randomLong) {
        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat"    => $randomLat,
                    "long"   => $randomLong,
                    "tipe"   => "in",
                    "status" => "dalam",
                    "foto"   => null,
                ]
            ],
            "query" => "mutation CreatePresence(\$createPresensiInput: CreatePresensiInput!) {
                createPresensi(createPresensiInput: \$createPresensiInput) {
                    presensi_id
                    employee_nip
                    presensi_tipe
                    presensi_date
                    presensi_time
                    presensi_lat
                    presensi_long
                    presensi_status
                    presensi_foto_url
                    presensi_foto_file_name
                    presensi_sync_eabsen
                    presensi_sync_eabsen_id
                    __typename
                }
                __typename
            }"
        ]);

        try {
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                throw new \Exception("Tidak dapat membuka file: {$filePath}");
            }

            $response = Http::timeout(30)
                ->asMultipart()
                ->withHeaders([
                    'apollo-require-preflight' => 'true',
                    'Authorization'            => 'Bearer ' . $user->api_token,
                    'User-Agent'              => 'Laravel-AutoCheckIn-Retry/1.0',
                ])
                ->attach('file', $fileHandle, $fileName)
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['errors']) && !empty($responseData['errors'])) {
                    $errorMessage = $responseData['errors'][0]['message'] ?? 'GraphQL Error';
                    $this->error("User ID {$user->id} retry masih error: {$errorMessage}");
                    $this->sendTelegramNotification($user, "Check-in gagal setelah retry: {$errorMessage}");
                    return false;
                }

                $this->info("User ID {$user->id} check-in berhasil setelah refresh token.");
                $this->sendTelegramNotification($user, 'Check-in otomatis berhasil setelah refresh token untuk ' . $user->name);
                return true;
            } else {
                $this->error("User ID {$user->id} retry gagal. Status: {$response->status()}");
                return false;
            }

        } catch (\Exception $e) {
            $this->error("Exception retry untuk user ID {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    private function generateRandomCoordinates($lat, $lng, $radius)
    {
        $radiusInDegrees = $radius / 111320; 
        $u = (float) rand() / (float) getrandmax();
        $v = (float) rand() / (float) getrandmax();
        $w = $radiusInDegrees * sqrt($u);
        $t = 2 * pi() * $v;
        $x = $w * cos($t);
        $y = $w * sin($t);

        $newLat = $lat + $y;
        $newLng = $lng + $x / cos($lat * (pi() / 180));

        return [round($newLat, 6), round($newLng, 6)];
    }
}
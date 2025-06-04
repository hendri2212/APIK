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
        // Validasi token
        $token = $user->api_token;
        if (!$token) {
            $this->error("Token tidak tersedia untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Token tidak tersedia');
            return false;
        }

        // Validasi koordinat
        if (!$user->latitude || !$user->longitude) {
            $this->error("Koordinat tidak tersedia untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in gagal: Koordinat tidak tersedia');
            return false;
        }

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

        [$randomLat, $randomLong] = $this->generateRandomCoordinates(
            $user->latitude, $user->longitude, $user->radius ?? 100
        );

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
            // Buat file handle baru setiap kali
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                throw new \Exception("Tidak dapat membuka file: {$filePath}");
            }

            $response = Http::timeout(30)
                ->retry(3, 1000) // Retry 3 kali dengan delay 1 detik
                ->asMultipart()
                ->withHeaders([
                    'apollo-require-preflight' => 'true',
                    'Authorization'            => 'Bearer ' . $token,
                    'User-Agent'              => 'Laravel-AutoCheckIn/1.0',
                ])
                ->attach('file', $fileHandle, $fileName)
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            // Tutup file handle
            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }

            $status = $response->status();
            $body = $response->body();

            // Log response untuk debugging
            Log::info("CheckIn API Response", [
                'user_id' => $user->id,
                'status' => $status,
                'body' => $body
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Periksa apakah response mengandung error GraphQL
                if (isset($responseData['errors']) && !empty($responseData['errors'])) {
                    $errorMessage = $responseData['errors'][0]['message'] ?? 'GraphQL Error';
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
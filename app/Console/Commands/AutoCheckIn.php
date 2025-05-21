<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Face;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class AutoCheckIn extends Command
{
    protected $signature = 'absen:auto-checkin';
    protected $description = 'Melakukan checkin otomatis untuk user dengan absent_type 1';

    public function handle() {
        $users = User::where('absent_type', 1)->get();

        foreach ($users as $user) {
            $this->checkinUser($user);
        }

        $this->info('Proses checkin otomatis selesai.');
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
            $response = \Http::post('https://telebot.saijaan.com/send', $payload);
            if (!$response->successful()) {
                \Log::warning('Gagal mengirim notifikasi Telegram: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('Exception saat mengirim notifikasi Telegram: ' . $e->getMessage());
        }
    }

    private function checkinUser($user) {
        $token = $user->api_token;
        if (!$token) {
            $this->error("Token tidak tersedia untuk user ID {$user->id}");
            return;
        }

        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $user->id)
            ->where('day', $currentDay)
            ->get();

        if ($faceImages->isEmpty()) {
            $this->error("Tidak ada gambar wajah tersedia untuk user ID {$user->id}");
            return;
        }

        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        if (!Storage::disk('private')->exists("face/{$fileName}")) {
            $this->error("File tidak ditemukan: face/{$fileName} untuk user ID {$user->id}");
            return;
        }

        $filePath = Storage::disk('private')->path("face/{$fileName}");

        [$randomLat, $randomLong] = $this->generateRandomCoordinates(
            $user->latitude, $user->longitude, $user->radius
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
            $response = Http::asMultipart()
                ->withHeaders([
                    'apollo-require-preflight' => 'true',
                    'Authorization'            => 'Bearer ' . $token,
                ])
                ->attach('file', fopen($filePath, 'r'), $fileName)
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            if ($response->failed()) {
                $this->error("Error server untuk user ID {$user->id}: " . $response->body());
                return;
            }

            $this->info("Checkout otomatis berhasil untuk user ID {$user->id}");
            $this->sendTelegramNotification($user, 'Check-in berhasil untuk ' . $user->name);
        } catch (\Exception $e) {
            $this->error("Exception untuk user ID {$user->id}: " . $e->getMessage());
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

        return [$newLat, $newLng];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Face;
use App\Models\User;
use Illuminate\Support\Arr;

class PresenceController extends Controller {
    public function index(Request $request) {
        return view('presence');
    }

    private function sendTelegramNotification($user, $message) {
        if (!$user->telegram_id) {
            \Log::info("User {$user->id} tidak memiliki telegram_id, notifikasi tidak dikirim.");
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
    
    public function generateRandomCoordinates($latitude, $longitude, $radiusInMeters) {
        $radiusInDegrees = $radiusInMeters / 111320;

        $u = mt_rand() / mt_getrandmax();
        $v = mt_rand() / mt_getrandmax();
        $w = $radiusInDegrees * sqrt($u);
        $t = 2 * pi() * $v;
        $newLat = $latitude + $w * cos($t);
        $newLong = $longitude + $w * sin($t) / cos(deg2rad($latitude));

        return [round($newLat, 7), round($newLong, 7)];
    }

    public function CheckIn(Request $request) {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $token = $user->api_token;
        if (!$token) {
            return response()->json(['error' => 'Token tidak tersedia.'], 403);
        }

        $centerLatitude = $user->latitude;
        $centerLongitude = $user->longitude;
        $radiusInMeters = $user->radius;

        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $userId)
            ->where('day', $currentDay)
            ->get();
        if ($faceImages->isEmpty()) {
            return response()->json(['error' => 'No images available.'], 404);
        }

        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        if (!Storage::disk('private')->exists("face/{$fileName}")) {
            return response()->json(['error' => "File tidak ditemukan: face/{$fileName}"], 404);
        }
        $filePath = Storage::disk('private')->path("face/{$fileName}");

        [$randomLat, $randomLong] = $this->generateRandomCoordinates($centerLatitude, $centerLongitude, $radiusInMeters);

        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat" => $randomLat,
                    "long" => $randomLong,
                    "tipe" => "in",
                    "status" => "dalam",
                    "foto" => null,
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
                ->attach(
                    'file', fopen($filePath, 'r'), $fileName
                )
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Error saat mengirim data ke server: ' . $response->body()], 500);
            }
            
            $this->sendTelegramNotification($user, 'Check-in berhasil untuk ' . $user->name);
            $this->sendWhatsappNotification($user, 'Check-in berhasil untuk ' . $user->name);
            return redirect()->route('history.today');
        } catch (\Exception $e) {
            return redirect()->route('history.today', [
                'data' => [],
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
        }
    }

    private function sendWhatsappNotification($user, $message) {
        if (!$user->no_hp) {
            \Log::info("User {$user->id} tidak memiliki no_hp, notifikasi WhatsApp tidak dikirim.");
            return;
        }

        $payload = [
            'to' => $user->no_hp,
            'message' => $message
        ];

        try {
            $response = \Http::post('https://wabot.tukarjual.com/send', $payload);
            if (!$response->successful()) {
                \Log::warning('Gagal mengirim notifikasi WhatsApp: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('Exception saat mengirim notifikasi WhatsApp: ' . $e->getMessage());
        }
    }

    public function CheckOut(Request $request) {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $token = $user->api_token;
        if (!$token) {
            return response()->json(['error' => 'Token tidak tersedia.'], 403);
        }

        $centerLatitude = $user->latitude;
        $centerLongitude = $user->longitude;
        $radiusInMeters = $user->radius;

        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $userId)
            ->where('day', $currentDay)
            ->get();
        if ($faceImages->isEmpty()) {
            return response()->json(['error' => 'No images available.'], 404);
        }

        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        if (!Storage::disk('private')->exists("face/{$fileName}")) {
            return response()->json(['error' => "File tidak ditemukan: face/{$fileName}"], 404);
        }
        $filePath = Storage::disk('private')->path("face/{$fileName}");

        [$randomLat, $randomLong] = $this->generateRandomCoordinates($centerLatitude, $centerLongitude, $radiusInMeters);

        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat" => $randomLat,
                    "long" => $randomLong,
                    "tipe" => "out",
                    "status" => "dalam",
                    "foto" => null,
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
                ->attach(
                    'file', fopen($filePath, 'r'), $fileName
                )
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "file" : ["variables.createPresensiInput.foto"] }',
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Error saat mengirim data ke server: ' . $response->body()], 500);
            }
            
            $this->sendTelegramNotification($user, 'Check-out berhasil untuk ' . $user->name);
            $this->sendWhatsappNotification($user, 'Check-out berhasil untuk ' . $user->name);
            return redirect()->route('history.today');
        } catch (\Exception $e) {
            return redirect()->route('history.today', [
                'data' => [],
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
        }
    }
}

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
        $tanggal = $request->input('tanggal', date('Y-m'));

        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                "operationName" => null,
                "variables" => [
                    "tanggal" => $tanggal
                ],
                "query" => "query FetchEabsenPresenceHistory(\$tanggal: String!) { presensiEabsenHistoryMe(tanggal: \$tanggal) { tanggal_masuk tanggal_keluar jam_masuk jam_keluar status_code status_name nip jam_mulai_absen_pagi jam_mulai_absen_pulang jenis_jadwal jam_mulai_kerja jam_pulang_kerja lewathari id jam_keluar_status jam_masuk_status presensi_apik { presensi_id employee_nip presensi_tipe presensi_date presensi_time presensi_lat presensi_long presensi_status presensi_foto_url presensi_foto_file_name presensi_sync_eabsen presensi_sync_eabsen_id __typename } __typename } __typename }"
            ]);

            if ($response->successful()) {
                $data = $response->json()['data']['presensiEabsenHistoryMe'];

                $data = array_filter($data, function ($item) {
                    $today = date('Y-m-d');
                    return isset($item['tanggal_masuk']) && $item['tanggal_masuk'] === $today;
                });

                $data = array_map(function ($item) {
                    $presensi_apik = $item['presensi_apik'] ?? [];

                    $absen_masuk = collect($presensi_apik)->firstWhere('presensi_tipe', 'IN');
                    $item['presensi_foto_masuk'] = $absen_masuk['presensi_foto_url'] ?? null;

                    $absen_keluar = collect($presensi_apik)->firstWhere('presensi_tipe', 'OUT');
                    $item['presensi_foto_keluar'] = $absen_keluar['presensi_foto_url'] ?? null;

                    return $item;
                }, $data);

                // return view('presence', compact('data', 'tanggal'));
                return view('presence', compact('data'));
            }
            return view('presence', [
                'data' => [],
                // 'tanggal' => $tanggal,
                'error' => 'API tidak merespons dengan benar. Silakan coba lagi.',
            ]);
        } catch (\Exception $e) {
            return view('presence', [
                'data' => [],
                // 'tanggal' => $tanggal,
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
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
                    '0', fopen($filePath, 'r'), $fileName
                )
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "0" : ["variables.createPresensiInput.foto"] }',
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Error saat mengirim data ke server: ' . $response->body()], 500);
            }

            return redirect()->route('presence');
        } catch (\Exception $e) {
            return redirect()->route('presence', [
                'data' => [],
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
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
                    '0', fopen($filePath, 'r'), $fileName
                )
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'operations' => $operations,
                    'map'        => '{ "0" : ["variables.createPresensiInput.foto"] }',
                ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Error saat mengirim data ke server: ' . $response->body()], 500);
            }

            return redirect()->route('presence');
        } catch (\Exception $e) {
            return redirect()->route('presence', [
                'data' => [],
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy($id) {
    }
}

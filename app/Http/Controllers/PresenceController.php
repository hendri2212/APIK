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
        $tanggal = $request->input('tanggal', date('Y-m')); // Menggunakan bulan saat ini sebagai default

        // Ambil token dari sesi
        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }
        
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

        // Ambil data dari respons API
        $data = $response->json()['data']['presensiEabsenHistoryMe'];

        $data = array_filter($data, function ($item) {
            $today = date('Y-m-d');
            return isset($item['tanggal_masuk']) && $item['tanggal_masuk'] === $today;
        });

        // Pisahkan absen masuk dan keluar untuk setiap entri
        $data = array_map(function ($item) {
            $presensi_apik = $item['presensi_apik'] ?? [];

            // Ambil absen masuk (IN)
            $absen_masuk = collect($presensi_apik)->firstWhere('presensi_tipe', 'IN');
            $item['presensi_foto_masuk'] = $absen_masuk['presensi_foto_url'] ?? null;

            // Ambil absen keluar (OUT)
            $absen_keluar = collect($presensi_apik)->firstWhere('presensi_tipe', 'OUT');
            $item['presensi_foto_keluar'] = $absen_keluar['presensi_foto_url'] ?? null;

            return $item;
        }, $data);

        // return view('presence', compact('data', 'tanggal'));
        return view('presence', compact('data'));
    }
    
    public function generateRandomCoordinates($latitude, $longitude, $radiusInMeters) {
        $radiusInDegrees = $radiusInMeters / 111320; // Konversi meter ke derajat
        $u = rand() / getrandmax();
        $v = rand() / getrandmax();

        $w = $radiusInDegrees * sqrt($u);
        $t = 2 * pi() * $v;

        $deltaLat = $w * cos($t);
        $deltaLong = $w * sin($t) / cos(deg2rad($latitude));

        $newLat = $latitude + $deltaLat;
        $newLong = $longitude + $deltaLong;

        // Membatasi presisi desimal menjadi 7 angka setelah koma
        $newLat = round($newLat, 7);
        $newLong = round($newLong, 7);

        return [$newLat, $newLong];
    }

    public function CheckIn(Request $request) {
        // Ambil token dari sesi
        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $userId = Session::get('user_id');

        // Ambil data lokasi dan radius dari tabel Users
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $centerLatitude = $user->latitude;
        $centerLongitude = $user->longitude;
        $radiusInMeters = $user->radius;

        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $userId)->where('day', $currentDay)->get();
        if ($faceImages->isEmpty()) {
            return response()->json(['error' => 'No images available.'], 404);
        }

        // Pilih gambar secara acak
        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        $filePath = storage_path("app/private/face/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => "File tidak ditemukan: $filePath"], 404);
        }

        [$randomLat, $randomLong] = $this->generateRandomCoordinates($centerLatitude, $centerLongitude, $radiusInMeters);

        $curl = curl_init();

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

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://gateway.apikv3.kalselprov.go.id/graphql',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'operations' => $operations,
                'map' => '{ "0" : ["variables.createPresensiInput.foto"] }',
                '0' => new \CURLFile($filePath)
            ],
            CURLOPT_HTTPHEADER => [
                'apollo-require-preflight: true',
                'Authorization: Bearer ' . $token
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => "cURL Error: $errorMessage"], 500);
        }

        curl_close($curl);
        // return response()->json(json_decode($response, true));
        return redirect()->route('presence');
    }

    public function CheckOut(Request $request) {
        // Ambil token dari sesi
        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $userId = Session::get('user_id');
        // Ambil data lokasi dan radius dari tabel Users
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $centerLatitude = $user->latitude;
        $centerLongitude = $user->longitude;
        $radiusInMeters = $user->radius;

        $currentDay = (string) Carbon::now()->dayOfWeek;
        $faceImages = Face::where('user_id', $userId)->where('day', $currentDay)->get();

        if ($faceImages->isEmpty()) {
            return response()->json(['error' => 'No images available.'], 404);
        }

        // Pilih gambar secara acak
        $randomFaceImage = Arr::random($faceImages->toArray());
        $fileName = $randomFaceImage['face_name'];

        $filePath = storage_path("app/private/face/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => "File tidak ditemukan: $filePath"], 404);
        }

        [$randomLat, $randomLong] = $this->generateRandomCoordinates($centerLatitude, $centerLongitude, $radiusInMeters);

        $curl = curl_init();

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

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://gateway.apikv3.kalselprov.go.id/graphql',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'operations' => $operations,
                'map' => '{ "0" : ["variables.createPresensiInput.foto"] }',
                '0' => new \CURLFile($filePath)
            ],
            CURLOPT_HTTPHEADER => [
                'apollo-require-preflight: true',
                'Authorization: Bearer ' . $token
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => "cURL Error: $errorMessage"], 500);
        }

        curl_close($curl);
        // return response()->json(json_decode($response, true));
        return redirect()->route('presence');
    }
}

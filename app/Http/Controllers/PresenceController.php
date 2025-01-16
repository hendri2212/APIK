<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Face;

class PresenceController extends Controller
{
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

        // Filter data untuk hanya menampilkan item dengan tanggal_masuk tidak kosong
        $data = array_filter($data, function ($item) {
            // return !empty($item['nip']);
            return $item['status_code'] == '1';
        });

        // Ambil 3 data terakhir
        $data = array_slice($data, -1);

        return view('presence', compact('data', 'tanggal'));
    }

    public function CheckIn(Request $request) {
        // Ambil token dari sesi
        $token = Session::get('api_token');
        // dd($token);
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $userId = Session::get('user_id');
        $faceImage = Face::where('user_id', $userId)->latest()->first();

        if (!$faceImage) {
            return response()->json(['error' => 'Face image not found.'], 404);
        }

        $fileName = $faceImage->face_name; // Pastikan ini sesuai dengan kolom di database
        $filePath = storage_path("app/private/face/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => "File tidak ditemukan: $filePath"], 404);
        }

        $curl = curl_init();

        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat" => -3.2252057,
                    "long" => 116.2475487,
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
        // dd($token);
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $userId = Session::get('user_id');
        $faceImage = Face::where('user_id', $userId)->latest()->first();

        if (!$faceImage) {
            return response()->json(['error' => 'Face image not found.'], 404);
        }

        $fileName = $faceImage->face_name; // Pastikan ini sesuai dengan kolom di database
        $filePath = storage_path("app/private/face/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['error' => "File tidak ditemukan: $filePath"], 404);
        }

        $curl = curl_init();

        $operations = json_encode([
            "operationName" => "CreatePresence",
            "variables" => [
                "createPresensiInput" => [
                    "lat" => -3.2252057,
                    "long" => 116.2475487,
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

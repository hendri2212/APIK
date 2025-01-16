<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PresenceController extends Controller
{
    public function CheckIn(Request $request) {
        // Ambil token dari sesi
        $token = Session::get('api_token');
        // dd($token);
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $filePath = storage_path('app/private/face/face01.jpg');

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
        return response()->json(json_decode($response, true));
    }

    public function CheckOut(Request $request) {
        // Ambil token dari sesi
        $token = Session::get('api_token');
        // dd($token);
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $filePath = storage_path('app/private/face/face01.jpg');

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
        return response()->json(json_decode($response, true));
    }
}

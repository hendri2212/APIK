<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function CheckIn(Request $request) {
        // $filePath = public_path('uploads/CAP34499864682227111.jpg'); // Sesuaikan path file Anda
        $filePath = 'face/face01.jpg'; // Path file di folder private
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan: ' . $filePath], 404);
        }

        // Konfigurasi data yang akan dikirim
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

        $map = json_encode([
            "0" => ["variables.createPresensiInput.foto"]
        ]);

        // Kirim permintaan ke API
        $response = Http::attach(
            '0', // Key untuk file
            file_get_contents($filePath),
            basename($filePath)
        )
        ->withHeaders([
            'apollo-require-preflight' => 'true',
            'Authorization' => 'Bearer ' . $token
        ])
        ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
            'operations' => $operations,
            'map' => $map,
        ]);

        // Periksa respons API
        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mengirim data ke API', 'details' => $response->json()], 500);
        }

        // Berikan respons
        return response()->json($response->json(), 200);
    }

    public function CheckOut(Request $request) {
        // $filePath = public_path('uploads/CAP34499864682227111.jpg'); // Sesuaikan path file Anda
        $filePath = 'face/face01.jpg'; // Path file di folder private
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan: ' . $filePath], 404);
        }

        // Konfigurasi data yang akan dikirim
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

        $map = json_encode([
            "0" => ["variables.createPresensiInput.foto"]
        ]);

        // Kirim permintaan ke API
        $response = Http::attach(
            '0', // Key untuk file
            file_get_contents($filePath),
            basename($filePath)
        )
        ->withHeaders([
            'apollo-require-preflight' => 'true',
            'Authorization' => 'Bearer ' . $token
        ])
        ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
            'operations' => $operations,
            'map' => $map,
        ]);

        // Periksa respons API
        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mengirim data ke API', 'details' => $response->json()], 500);
        }

        // Berikan respons
        return response()->json($response->json(), 200);
    }
}

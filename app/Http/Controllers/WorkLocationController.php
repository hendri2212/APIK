<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class WorkLocationController extends Controller {
    public function index() {
        // URL endpoint
        $url = 'https://gateway.apikv3.kalselprov.go.id/graphql';

        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        // GraphQL query
        $query = [
            'operationName' => null,
            'variables' => (object) [],
            'query' => <<<GQL
                query FetchPresenceLocation {
                    lokasiKerjaMe {
                        id
                        nip
                        nama
                        kode
                        idinsinduk
                        namainstansiinduk
                        idins
                        namainstansi
                        latitude
                        longitude
                        radius
                        __typename
                    }
                    __typename
                }
            GQL,
        ];

        // Request ke API menggunakan Facade Http
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $query);

        // Mengembalikan response
        if ($response->successful()) {
            $data = $response->json()['data']['lokasiKerjaMe'] ?? null;
            return view('workLocation', compact('data'));
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data',
                'error' => $response->json(),
            ], $response->status());
        }
    }
}

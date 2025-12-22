<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller {
    public function index() {
        // URL endpoint
        $url = 'https://gateway.apikv3.kalselprov.go.id/graphql';

        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $userId = Session::get('user_id');
        $nip = $userId == 1 ? '199108022024211014' : '199011042022211001';

        // GraphQL query
        $query = [
            'operationName' => null,
            'variables' => [
                'nip' => $nip,
            ],
            'query' => <<<GQL
                query FetchSimpegData(\$nip: String!) {
                    simpegIdentitas(nip: \$nip) {
                        nip
                        nama
                        gelar_depan
                        gelar_belakang
                        jenis_kelamin
                        tempat_lahir
                        tanggal_lahir
                        agama
                        jenis
                        jenis_jabatan
                        tingkat_jabatan
                        golongan
                        pangkat
                        kode_jabatan
                        jabatan
                        kode_satker
                        satker
                        kode_unker
                        unker
                        instansi
                        ktpu
                        jurusan
                        status_pns
                        email
                        alamat
                        telpon
                        images
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
            $data = $response->json()['data']['simpegIdentitas'] ?? null;
            return view('auth.profile', compact('data'));
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data',
                'error' => $response->json(),
            ], $response->status());
        }
    }
}

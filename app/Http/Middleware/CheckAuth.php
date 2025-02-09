<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        // if (!Session::has('api_token')) {
        //     return redirect()->route('login')->withErrors(['authError' => 'Silakan login terlebih dahulu.']);
        // }
        // return $next($request);



        // Ambil access token dari session
        $accessToken = Session::get('api_token');

        // Jika token tidak ada atau sudah expired, lakukan refresh
        if (!$accessToken || $this->isTokenExpired($accessToken)) {
            // Ambil refresh token dari session
            $refreshToken = Session::get('refresh_token');
            if (!$refreshToken) {
                return redirect()->route('login')->withErrors(['authError' => 'Silakan login terlebih dahulu.']);
            }

            // Inisialisasi Guzzle Client
            $client = new Client();
            $graphqlEndpoint = 'https://gateway.apikv3.kalselprov.go.id/graphql';

            // Format mutation untuk refresh token
            $mutation = '
                mutation RefreshToken($refreshTokenInput: RefreshTokenInput!) {
                  refreshToken(refreshTokenInput: $refreshTokenInput) {
                    access_token
                    refresh_token
                    userdata {
                      employee_nip
                      user_id
                      user_level
                      __typename
                    }
                    __typename
                  }
                  __typename
                }
            ';

            $variables = [
                'refreshTokenInput' => [
                    'refresh_token' => $refreshToken
                ]
            ];

            try {
                // Kirim request POST ke GraphQL endpoint
                $response = $client->post($graphqlEndpoint, [
                    'json' => [
                        'query'     => $mutation,
                        'variables' => $variables,
                    ]
                ]);

                $body = json_decode($response->getBody(), true);

                // Cek apakah response mengandung data refreshToken
                if (isset($body['data']['refreshToken'])) {
                    $newTokens = $body['data']['refreshToken'];
                    // Simpan token baru ke session
                    Session::put('api_token', $newTokens['access_token']);
                    Session::put('refresh_token', $newTokens['refresh_token']);
                } else {
                    // Jika response tidak mengembalikan token baru, arahkan ke login
                    return redirect()->route('login')->withErrors(['authError' => 'Gagal memperbarui token, silakan login kembali.']);
                }
            } catch (\Exception $e) {
                // Tangani error request atau exception lain
                return redirect()->route('login')->withErrors(['authError' => 'Gagal memperbarui token: ' . $e->getMessage()]);
            }
        }

        // Jika token valid atau sudah diperbarui, lanjutkan request
        return $next($request);
    }

    private function isTokenExpired($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return true; // Token tidak valid
        }

        // Perbaiki string base64 jika perlu
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        if (!$payload || !isset($payload['exp'])) {
            return true;
        }

        // Cek apakah waktu sekarang lebih besar dari waktu expired di token
        return $payload['exp'] < time();
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        // Cari user berdasarkan username
        $user = \App\Models\User::where('username', $request->username)->first();
    
        // Periksa apakah user ditemukan
        if (!$user) {
            return back()->withErrors(['loginError' => 'Username tidak ditemukan.']);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
            "operationName" => null,
            "variables" => [
                "loginMobileAuthInput" => [
                    "username" => $request->username,
                    "password" => $request->password,
                    "uuid" => $user->uuid, // Ambil UUID dari database
                ]
            ],
            "query" => "mutation LoginMobile(\$loginMobileAuthInput: LoginMobileAuthInput!) { loginMobile(loginMobileAuthInput: \$loginMobileAuthInput) { access_token refresh_token userdata { employee_nip user_id user_level __typename } __typename } __typename }"
        ]);

        // Cek apakah respons berhasil dan terdapat token akses
        if ($response->successful() && isset($response['data']['loginMobile']['access_token'])) {
            $accessToken = $response['data']['loginMobile']['access_token'];

            // Simpan token dalam sesi
            Session::put([
                'api_token' => $accessToken,
                'user_id' => $user->id,
            ]);
            // dd(Session::get('api_token'));

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['loginError' => 'Login gagal, periksa kembali kredensial Anda.']);
    }

    public function logout() {
        Session::forget('api_token');
        return redirect()->route('login');
    }
}
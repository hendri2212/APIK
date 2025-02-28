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

        if ($isExpired = $user->expired < now()->format('Y-m-d')) {
            return back()->withErrors(['loginError' => 'Akun expired.']);
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
            $refreshToken = $response['data']['loginMobile']['refresh_token'];

            // Update token pada tabel users
            $user->update([
                'api_token'     => $accessToken,
                'refresh_token' => $refreshToken,
            ]);

            // Simpan token dalam sesi
            Session::put([
                'api_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user_id' => $user->id,
                'full_name' => $user->name,
                'expired' => $user->expired,
            ]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['loginError' => 'Login gagal, periksa kembali kredensial Anda.']);
    }

    public function logout() {
        Session::forget(['api_token', 'user_id', 'full_name', 'expired']);
    
        // Opsional: Gunakan flush jika ingin membersihkan semua data sesi
        Session::flush();
    
        // Redirect ke halaman login
        return redirect()->route('login')->with('message', 'You have been logged out successfully.');
    }
}
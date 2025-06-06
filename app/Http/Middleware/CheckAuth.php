<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\TokenRefreshService;

class CheckAuth
{
    public function handle(Request $request, Closure $next) {
        $accessToken = Session::get('api_token');

        // Jika token tidak ada atau sudah expired, lakukan refresh
        if (!$accessToken || TokenRefreshService::isTokenExpired($accessToken)) {
            
            // Coba refresh token menggunakan service
            if (!TokenRefreshService::refreshSessionToken()) {
                return redirect()->route('login')->withErrors(['authError' => 'Silakan login kembali.']);
            }
        }

        // Jika token valid atau sudah diperbarui, lanjutkan request
        return $next($request);
    }
}
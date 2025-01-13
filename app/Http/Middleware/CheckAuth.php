<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login')->withErrors(['authError' => 'Silakan login terlebih dahulu.']);
        }
        return $next($request);
    }
}
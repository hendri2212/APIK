<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TokenRefreshService
{
    /**
     * Check if token is expired
     */
    public static function isTokenExpired($token)
    {
        if (!$token) {
            return true;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return true; // Token tidak valid
        }

        // Decode JWT payload
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        if (!$payload || !isset($payload['exp'])) {
            return true;
        }

        // Cek apakah waktu sekarang lebih besar dari waktu expired di token
        // Tambahkan buffer 5 menit sebelum expired
        return $payload['exp'] < (time() + 300);
    }

    /**
     * Refresh token untuk user tertentu (untuk console commands)
     */
    public static function refreshUserToken(User $user)
    {
        if (!$user->refresh_token) {
            Log::warning("User ID {$user->id} tidak memiliki refresh token");
            return false;
        }

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
                'refresh_token' => $user->refresh_token
            ]
        ];

        try {
            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                    'query' => $mutation,
                    'variables' => $variables,
                ]);

            if (!$response->successful()) {
                Log::error("Refresh token failed for user {$user->id}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            $body = $response->json();

            // Cek apakah response mengandung data refreshToken
            if (isset($body['data']['refreshToken'])) {
                $newTokens = $body['data']['refreshToken'];

                // Update token pada database
                $user->update([
                    'api_token' => $newTokens['access_token'],
                    'refresh_token' => $newTokens['refresh_token'],
                ]);

                Log::info("Token berhasil di-refresh untuk user {$user->id}");
                return true;
            } else {
                // Cek jika ada error dalam response
                if (isset($body['errors'])) {
                    $errorMessage = $body['errors'][0]['message'] ?? 'Unknown GraphQL error';
                    Log::error("GraphQL error saat refresh token user {$user->id}: {$errorMessage}");
                } else {
                    Log::error("Response tidak mengandung data refreshToken untuk user {$user->id}", $body);
                }
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Exception saat refresh token user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh token untuk session (untuk middleware)
     */
    public static function refreshSessionToken()
    {
        $refreshToken = Session::get('refresh_token');
        if (!$refreshToken) {
            return false;
        }

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
            $response = Http::timeout(30)->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                'query' => $mutation,
                'variables' => $variables,
            ]);

            if (!$response->successful()) {
                return false;
            }

            $body = $response->json();

            if (isset($body['data']['refreshToken'])) {
                $newTokens = $body['data']['refreshToken'];

                // Update session
                Session::put('api_token', $newTokens['access_token']);
                Session::put('refresh_token', $newTokens['refresh_token']);

                // Update database juga
                if (Session::has('user_id')) {
                    $user = User::find(Session::get('user_id'));
                    if ($user) {
                        $user->update([
                            'api_token' => $newTokens['access_token'],
                            'refresh_token' => $newTokens['refresh_token'],
                        ]);
                    }
                }

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Exception saat refresh session token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure user has valid token (refresh if needed)
     */
    public static function ensureValidToken(User $user)
    {
        // Jika token tidak ada atau expired, coba refresh
        if (!$user->api_token || self::isTokenExpired($user->api_token)) {
            return self::refreshUserToken($user);
        }

        return true; // Token masih valid
    }
}
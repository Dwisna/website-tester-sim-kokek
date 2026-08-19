<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyClientToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil token dari header Authorization: Bearer <token>
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 401);
        }

        // Cek apakah token ada di cache
        $clientId = Cache::get('api_token_' . $token);

        if (!$clientId) {
            return response()->json(['message' => 'Token tidak valid atau sudah kedaluwarsa.'], 401);
        }

        return $next($request);
    }
}
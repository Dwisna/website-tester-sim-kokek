<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyN8nSecret
{
    public function handle(Request $request, Closure $next)
    {
        $secret = $request->header('X-N8N-Secret');
        $expected = (string) config('services.n8n.secret');

        Log::info('N8N Secret Debug', [
            'received_length' => strlen((string) $secret),
            'expected_length' => strlen($expected),
            'received_preview' => substr((string) $secret, 0, 4) . '...' . substr((string) $secret, -4),
            'expected_preview' => substr($expected, 0, 4) . '...' . substr($expected, -4),
        ]);

        if (!$secret || !hash_equals($expected, (string) $secret)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

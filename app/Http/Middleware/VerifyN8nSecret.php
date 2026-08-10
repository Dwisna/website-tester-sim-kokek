<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyN8nSecret
{
    public function handle(Request $request, Closure $next)
    {
        $secret = $request->header('X-N8N-Secret');

        if (!$secret || !hash_equals((string) config('services.n8n.secret'), (string) $secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
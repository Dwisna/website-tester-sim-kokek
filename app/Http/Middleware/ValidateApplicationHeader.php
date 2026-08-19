<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApplicationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil nama header dari config/rup.php (default: X-Rup-Application-Token)
        $headerName = config('rup.token_header', 'X-Rup-Application-Token');

        // Cek apakah header tersebut dikirim oleh client
        if (!$request->hasHeader($headerName)) {
            return response()->json([
                'success' => false,
                'message' => "Header '{$headerName}' wajib disertakan pada request ini."
            ], 400);
        }

        return $next($request);
    }
}
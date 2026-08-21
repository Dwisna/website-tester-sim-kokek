<?php

namespace App\Http\Controllers;

use App\Models\ApiServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    public function issueToken(Request $request)
    {
        // 1. Cek Kewajiban HTTPS (Hanya aktif jika RUP_API_REQUIRE_HTTPS=true di .env)
        if (config('rup.require_https') && !$request->isSecure()) {
            return response()->json([
                'success' => false,
                'message' => 'Protokol HTTPS diwajibkan untuk endpoint ini.'
            ], 403);
        }

        // 2. Validasi input
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'purpose' => 'required|string'
        ]);

        // 3. Cari client berdasarkan client_id
        $client = ApiServiceClient::where('client_id', $request->client_id)->first();

        // 4. Validasi status aktif & kecocokan secret
        if (!$client || !$client->isUsable() || !Hash::check($request->client_secret, $client->secret_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial Client ID atau Secret tidak valid.'
            ], 401);
        }

        // 5. Cek IP Allowlist (Hanya aktif jika RUP_API_REQUIRE_CLIENT_IP_ALLOWLIST=true di .env)
        if (config('rup.require_ip_allowlist')) {
            $clientIp = $request->ip();
            $allowedIps = $client->allowed_ips ?? [];

            if (!empty($allowedIps) && !in_array($clientIp, $allowedIps, true)) {
                return response()->json([
                    'success' => false,
                    'message' => "IP Client ({$clientIp}) tidak diizinkan mengakses API ini."
                ], 403);
            }
        }

        // 6. Validasi purpose (menggunakan helper method model)
        if (!$client->allowsPurpose($request->purpose)) {
            return response()->json([
                'success' => false,
                'message' => 'Client tidak memiliki izin untuk purpose ini.'
            ], 403);
        }

        // 7. Ambil TTL token dinamis dari config/rup.php (default 60 menit)
        $ttlMinutes = (int) config('rup.token_ttl_minutes', 60);
        $expiresAt = now()->addMinutes($ttlMinutes);

        // Ambil abilities yang diizinkan client (default wildcard '*' jika kosong)
        $abilities = $client->allowed_abilities ?? ['*'];

        // 8. Buat Token Resmi Laravel Sanctum
        $token = $client->createToken('gateway-token', $abilities, $expiresAt);

        // 9. Kembalikan response JSON
        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_in' => $ttlMinutes * 60,
            'expires_at' => $expiresAt->setTimezone('Asia/Jakarta')->format('d M Y H:i:s')
        ]);
    }
}
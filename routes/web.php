<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Server is running',
    ]);
});

Route::get('/run', function () {
    try {
        // 1. Tentukan koneksi sementara langsung ke Database Server Navicat
        Config::set('database.connections.server_kokek', [
            'driver'    => 'mysql',
            'host'      => '8.215.110.218',
            'port'      => 3306,
            'database'  => 'kokek_api',
            'username'  => 'kokek_it',
            'password'  => '7y8LBnXIr09j',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);

        $conn = DB::connection('server_kokek');

        // 2. Generate Client ID & Secret Asli (Plain text)
        $rawClientId     = 'rup_client_' . Str::random(16);
        $rawClientSecret = 'secret_' . Str::random(32);

        // 3. Cek struktur kolom di tabel database server
        $columns = $conn->getSchemaBuilder()->getColumnListing('api_service_clients');

        // Deteksi nama kolom secret yang dipakai
        $secretColumn = 'secret';
        if (in_array('client_secret', $columns)) {
            $secretColumn = 'client_secret';
        } elseif (in_array('secret', $columns)) {
            $secretColumn = 'secret';
        } elseif (in_array('secret_hash', $columns)) {
            $secretColumn = 'secret_hash';
        }

        // Susun payload insert/update sesuai kolom yang ada di database
        $data = [
            'client_id'  => $rawClientId,
            $secretColumn => Hash::make($rawClientSecret),
            'updated_at' => now(),
            'created_at' => now(),
        ];

        if (in_array('purpose', $columns))     $data['purpose'] = 'gateway';
        if (in_array('allowed_ips', $columns)) $data['allowed_ips'] = null;
        if (in_array('is_active', $columns))   $data['is_active'] = 1;
        if (in_array('status', $columns))      $data['status'] = 'active';
        if (in_array('expires_at', $columns))  $data['expires_at'] = now()->addYears(2);

        // 4. Update atau Insert
        $conn->table('api_service_clients')->updateOrInsert(
            ['name' => 'Staging Web Client Prod'],
            $data
        );

        // 5. Tampilkan Kredensial Asli
        return response()->json([
            'status'  => 'success',
            'message' => 'Client ID & Secret berhasil dibuat dan disimpan ke database server!',
            'credentials_for_env_project_2' => [
                'API_SERVER_BASE_URL'     => 'https://api.kokek.com/api',
                'API_SERVER_CLIENT_ID'     => $rawClientId,
                'API_SERVER_CLIENT_SECRET' => $rawClientSecret,
            ],
            'note' => 'Salin kedua nilai di atas langsung ke file .env Project 2 Anda.'
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal: ' . $e->getMessage(),
        ], 500);
    }
});
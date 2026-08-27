<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Server is running',
    ]);
});

// Route untuk generate token dan menampilkan respons terminal
Route::get('/run', function () {
    try {
        Artisan::call('rup:service-client:create', [
            'email'  => 'test@example.com', 
            '--name' => 'RUP Web Client',
            // IP wajib ditulis dalam bentuk array agar tidak memicu error implode()
            '--ip' => ['8.215.106.90'], 
            '--days' => 365,
        ]);

        // Mengambil teks respons terminal
        $output = Artisan::output(); 

        return response('<pre>' . $output . '</pre>');

    } catch (\Exception $e) {
        return 'Gagal menjalankan perintah: ' . $e->getMessage();
    }
});
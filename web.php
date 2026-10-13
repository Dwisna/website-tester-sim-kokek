<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// ==========================
// Dashboard
// ==========================

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/records/{record}', [DashboardController::class, 'showRecord'])
    ->name('records.show');

Route::get('/history', [DashboardController::class, 'historyPage'])
    ->name('history');

Route::get('/notifications', [DashboardController::class, 'notificationsPage'])
   ->name('notifications');

Route::get('/openclaw', [DashboardController::class, 'openclawPage'])
    ->name('openclaw');

// ==========================
// API Internal Dashboard
// ==========================

Route::prefix('api')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
    Route::get('/dashboard/weekly-trend', [DashboardController::class, 'weeklyTrendApi'])->name('api.weekly-trend');
    Route::get('/dashboard/latest-scraping', [DashboardController::class, 'latestScrapingApi'])->name('api.latest-scraping');

    Route::match(['get', 'post'], '/chat', [DashboardController::class, 'chatApi']);

    //Route::get('/history', [DashboardController::class, 'historyApi']);
    //Route::get('/download', [DashboardController::class, 'downloadApi']);

    Route::get('/notifications', [DashboardController::class, 'notificationsApi']);

});

// ==========================
// Admin Management
// ==========================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', function () {
            return 'Admin Panel';
        })->name('admin.dashboard');

    });

// ==========================
// Debug Koneksi Token
// ==========================
Route::get('/debug-token', function () {
    $url = rtrim(env('API_SERVER_BASE_URL'), '/');
    $id  = env('API_SERVER_CLIENT_ID');
    $sec = env('API_SERVER_CLIENT_SECRET');

    try {
        $res = Http::withoutVerifying()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json',
            ])
            ->post("{$url}/service/token", [
                'client_id'     => $id,
                'client_secret' => $sec,
                'purpose'       => 'gateway',
            ]);

        return response()->json([
            'status_kode'       => $res->status(),
            'url_tujuan'        => "{$url}/service/token",
            'client_id_dibaca'  => $id,
            'respons_project_1' => $res->json() ?? $res->body(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error'   => 'Koneksi gagal / timeout',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// ==========================
// Debug Tarik Data Dashboard
// ==========================
Route::get('/debug-data', function () {
    $url   = rtrim(env('API_SERVER_BASE_URL'), '/');
    $token = '51|lV4Hr2HGAI4AqQ815MIodxTvexC7VeRJ5z8D9qgv0980e3b3';

    try {
        $res = Http::withoutVerifying()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json',
            ])
            ->withToken($token)
            ->get("{$url}/dashboard");

        return response()->json([
            'status_kode'   => $res->status(),
            'url_dipanggil' => "{$url}/dashboard",
            'data'          => $res->json() ?? $res->body(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error'   => 'Gagal mengambil data',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// ==========================
// Debug Otomatis (Langkah 1)
// ==========================
Route::get('/debug-auto', function (\App\Services\RupApiClient $apiClient) {
    try {
        $token = $apiClient->getToken(true);
        $data  = $apiClient->getDashboardData();

        return response()->json([
            'status'        => 'BERHASIL OTOMATIS',
            'token_didapat' => substr($token, 0, 15) . '...',
            'total_data'    => $data['data']['pagination']['total'] ?? count($data['data']['records'] ?? []),
            'sample_data'   => $data['data']['records'][0] ?? null,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'GAGAL',
            'pesan'   => $e->getMessage(),
            'file'    => $e->getFile() . ':' . $e->getLine(),
        ], 500);
    }
});

Route::get('/clear-all-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return 'Semua cache (Cache data, Config, View, Route) berhasil dibersihkan!';
});

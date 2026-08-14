<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ==========================
// Website Authentication
// ==========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');
});

// ==========================
// Authenticated Website
// ==========================
// NOTE: To temporarily disable authentication for local testing,
// you can replace the following line with:
// Route::group([], function () {
// and restore to Route::middleware('auth')->group(function () { when done.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/records/{record}', [DashboardController::class, 'showRecord'])->name('records.show');
    Route::get('/history', [DashboardController::class, 'historyPage'])->name('history');
    Route::get('/notifications', [DashboardController::class, 'notificationsPage'])->name('notifications');
    Route::get('/openclaw', [DashboardController::class, 'openclawPage'])->name('openclaw');

    // Endpoint internal dashboard tetap memakai session login.
    // API eksternal n8n akan dipindahkan ke Sanctum pada Phase 2.
    Route::prefix('api')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
        Route::match(['get', 'post'], '/chat', [DashboardController::class, 'chatApi']);
        Route::get('/history', [DashboardController::class, 'historyApi']);
        Route::get('/download', [DashboardController::class, 'downloadApi']);
        Route::get('/notifications', [DashboardController::class, 'notificationsApi']);
        Route::get('/api/download', [DashboardController::class, 'download'])->name('rup.download');
    });
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
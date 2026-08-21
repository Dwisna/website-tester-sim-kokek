<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// publik (tanpa token)
Route::post('/service/token', [TokenController::class, 'issueToken']);
Route::post('/n8n/webhook', [DashboardController::class, 'n8nWebhook']);

// Pakai Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Dashboard & List Data RUP (API)
    Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
    
    // 2. Detail Record
    Route::get('/records/{id}', [DashboardController::class, 'showRecordApi'])->name('records.show');
    
    // 3. Post Import Data (dari n8n / client)
    Route::post('/n8n/import', [DashboardController::class, 'n8nImport']);
    
    // 4. Log Webhook, Notifikasi, dan Demo Chat
    Route::get('/history', [DashboardController::class, 'historyApi']);
    Route::get('/notifications', [DashboardController::class, 'notificationsApi']);
    
    // 5. Download Excel
    Route::get('/download', [DashboardController::class, 'download'])->name('rup.download');
});

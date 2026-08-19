<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TokenController;
use app\Http\Middleware\ValidateApplicationHeader;
use Illuminate\Support\Facades\Route;

// publik (tanpa token)
Route::post('/service/token', [TokenController::class, 'issueToken']);
Route::post('/n8n/webhook', [DashboardController::class, 'n8nWebhook']);

// Pakai Scantum
Route::middleware('auth:sanctum, ValidateApplicationHeader::class')->group(function () {
    
    // 1. Dashboard & List Data RUP
    Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
    
    // 2. Detail Record
    Route::get('/records/{id}', [DashboardController::class, 'showRecordApi']);
    
    // 3. Post Import Data (dari n8n / client)
    Route::post('/n8n/import', [DashboardController::class, 'n8nImport']);
    
    // 4. Log Webhook, Notifikasi, dan Demo Chat
    Route::get('/history', [DashboardController::class, 'historyApi']);
    Route::get('/notifications', [DashboardController::class, 'notificationsApi']);
    
    // 5. Download Excel
    Route::get('/download', [DashboardController::class, 'download'])->name('rup.download');
});
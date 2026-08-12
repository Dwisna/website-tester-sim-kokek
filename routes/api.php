<?php
// use App\Http\Controllers\RupController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


    Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
    Route::match(['get', 'post'], '/chat', [DashboardController::class, 'chatApi']);
    Route::get('/history', [DashboardController::class, 'historyApi']);
    Route::get('/download', [DashboardController::class, 'downloadApi']);
    Route::get('/notifications', [DashboardController::class, 'notificationsApi']);
    Route::get('/api/download', [DashboardController::class, 'download'])->name('rup.download');


    Route::post('/n8n/import', [DashboardController::class, 'n8nImport']);
    Route::post('/n8n/webhook', [DashboardController::class, 'n8nWebhook']);
<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
Route::match(['get', 'post'], '/chat', [DashboardController::class, 'chatApi']);
Route::post('/n8n/webhook', [DashboardController::class, 'n8nWebhook']);
Route::post('/n8n/import', [DashboardController::class, 'n8nImport']);
Route::get('/history', [DashboardController::class, 'historyApi']);
Route::get('/download', [DashboardController::class, 'downloadApi']);
Route::get('/notifications', [DashboardController::class, 'notificationsApi']);

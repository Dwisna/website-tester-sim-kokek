<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
Route::match(['get', 'post'], '/chat', [DashboardController::class, 'chatApi']);
Route::post('/n8n/webhook', [DashboardController::class, 'n8nWebhook']);
Route::get('/history', [DashboardController::class, 'historyApi']);
Route::get('/download', [DashboardController::class, 'downloadApi']);
Route::get('/notifications', [DashboardController::class, 'notificationsApi']);

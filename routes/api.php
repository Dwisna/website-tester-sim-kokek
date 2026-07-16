<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'dashboardApi']);
Route::get('/chat', [DashboardController::class, 'chatApi']);
Route::get('/history', [DashboardController::class, 'historyApi']);
Route::get('/download', [DashboardController::class, 'downloadApi']);
Route::get('/notifications', [DashboardController::class, 'notificationsApi']);

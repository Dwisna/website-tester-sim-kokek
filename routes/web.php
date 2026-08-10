<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('dashboard.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/records/{record}', [DashboardController::class, 'showRecord'])->name('records.show');
    Route::get('/history', [DashboardController::class, 'historyPage'])->name('history');
    Route::get('/notifications', [DashboardController::class, 'notificationsPage'])->name('notifications');
});
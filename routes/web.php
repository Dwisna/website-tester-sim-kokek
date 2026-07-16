<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/records/{record}', [DashboardController::class, 'showRecord'])->name('records.show');
Route::get('/openclaw', [DashboardController::class, 'openclawPage'])->name('openclaw');

<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Server is running',
    ]);
});

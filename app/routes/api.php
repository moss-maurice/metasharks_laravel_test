<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('rooms')->group(function () {
    Route::get('list', [RoomController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('order', [RoomOrderController::class, 'store']);
    });
});

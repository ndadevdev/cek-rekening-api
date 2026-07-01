<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CekRekeningController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->withoutMiddleware('throttle:api');

Route::get('/banks', [CekRekeningController::class, 'daftarBank']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::post('/cek-rekening', [CekRekeningController::class, 'cek']);
});

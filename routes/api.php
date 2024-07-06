<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('auth/token', [AuthController::class, 'token']);
        Route::controller(BookController::class)->prefix('/books')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
        });
    });
});

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthController::class, 'token']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(BookController::class)->prefix('/books')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::post('{bookId}/import-indices-xml', 'importIndices');
        });
    });
});

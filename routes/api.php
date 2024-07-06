<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthController::class, 'token']);
});

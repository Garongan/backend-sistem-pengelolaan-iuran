<?php

// No middleware

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// api middleware
Route::middleware('auth:api')->group(function () {
    // auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // users
    Route::prefix('users')->group(function () {
        Route::get('me', [UserController::class, 'me']);
        Route::get('', [UserController::class, 'index']);
    });

    // residents
    Route::resource('residents', ResidentController::class);

    // images
    Route::get('images', [ImageController::class, 'show']);
});

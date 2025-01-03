<?php

// No middleware

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HouseController;
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

    // houses
    Route::prefix('houses')->group(function () {
        Route::get('', [HouseController::class, 'index']);
        Route::post('', [HouseController::class, 'store']);
        Route::get('{id}', [HouseController::class, 'show']);
        Route::put('{id}', [HouseController::class, 'update']);
    });

    // residents
    Route::prefix('residents')->group(function () {
        Route::get('', [ResidentController::class, 'index']);
        Route::post('', [ResidentController::class, 'store']);
        Route::get('{id}', [ResidentController::class, 'show']);
        Route::put('{id}', [ResidentController::class, 'update']);
    });

    // images
    Route::get('images', [ImageController::class, 'show']);
});

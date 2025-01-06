<?php

// No middleware

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\HouseResidentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
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
    Route::prefix('residents')->group(function () {
        Route::get('', [ResidentController::class, 'index']);
        Route::post('', [ResidentController::class, 'store']);
        Route::get('{id}', [ResidentController::class, 'show']);
        Route::put('{id}', [ResidentController::class, 'update']);
        Route::delete('{id}', [ResidentController::class, 'destroy']);
    });

    // houses
    Route::prefix('houses')->group(function () {
        Route::get('', [HouseController::class, 'index']);
        Route::post('', [HouseController::class, 'store']);
        Route::get('{id}', [HouseController::class, 'show']);
        Route::put('{id}', [HouseController::class, 'update']);
        Route::post('add-resident/{id}', [HouseController::class, 'addResident']);
        Route::patch('delete-resident/{id}', [HouseController::class, 'deleteResident']);
    });

    // payments
    Route::prefix('payments')->group(function () {
        Route::get('', [PaymentController::class, 'index']);
        Route::post('', [PaymentController::class, 'store']);
        Route::get('{id}', [PaymentController::class, 'show']);
        Route::put('{id}', [PaymentController::class, 'update']);
        Route::delete('{id}', [PaymentController::class, 'destroy']);
    });

    // expenses
    Route::prefix('expenses')->group(function () {
        Route::get('', [ExpenseController::class, 'index']);
        Route::post('', [ExpenseController::class, 'store']);
        Route::get('{id}', [ExpenseController::class, 'show']);
        Route::put('{id}', [ExpenseController::class, 'update']);
        Route::delete('{id}', [ExpenseController::class, 'destroy']);
    });

    // reports
    Route::prefix('reports')->group(function () {
        Route::get('monthly', [ReportController::class, 'monthlySummary']);
        Route::get('yearly', [ReportController::class, 'yearlySummary']);
        Route::get('download/monthly', [ReportController::class, 'downloadMonthlySummary']);
        Route::get('download/yearly', [ReportController::class, 'downloadYearlySummary']);
    });
});

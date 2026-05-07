<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DocAppoint API Routes
|--------------------------------------------------------------------------
|
| This app is currently Inertia-first. Keep the API routes aligned with the
| controllers that exist in the repository so artisan route discovery works.
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorController::class, 'index']);
        Route::get('/{doctor}', [DoctorController::class, 'show']);
        Route::get('/{doctor}/slots', [DoctorController::class, 'slots']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::middleware('role:patient')->prefix('appointments')->group(function () {
            Route::post('/', [AppointmentController::class, 'store']);
            Route::patch('/{appointment}/cancel', [AppointmentController::class, 'cancel']);
            Route::post('/{appointment}/complete', [AppointmentController::class, 'complete']);
        });

        Route::middleware('role:doctor')->prefix('appointments')->group(function () {
            Route::patch('/{appointment}/accept', [AppointmentController::class, 'accept']);
            Route::patch('/{appointment}/decline', [AppointmentController::class, 'decline']);
        });

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/verifications', [AdminController::class, 'verifications']);
            Route::patch('/doctors/{doctor}/approve', [AdminController::class, 'approve']);
            Route::patch('/doctors/{doctor}/reject', [AdminController::class, 'reject']);
        });
    });
});

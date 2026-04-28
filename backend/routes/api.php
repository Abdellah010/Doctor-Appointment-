<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| DocAppoint API Routes — Laravel 11
|--------------------------------------------------------------------------
*/

// ── Public routes ──────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/register',       [AuthController::class, 'register']);
        Route::post('/login',          [AuthController::class, 'login']);
        Route::post('/forgot-password',[AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/google',          [SocialAuthController::class, 'redirectToGoogle']);
        Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    });

    // Public doctor browsing
    Route::prefix('doctors')->group(function () {
        Route::get('/',                [DoctorController::class, 'index']);
        Route::get('/{doctor}',        [DoctorController::class, 'show']);
        Route::get('/{doctor}/slots',  [SlotController::class, 'available']);
        Route::get('/{doctor}/reviews',[ReviewController::class, 'forDoctor']);
    });

    // Specialties
    Route::get('/specialties', fn() => \App\Models\Specialty::all());

    // ── Authenticated routes ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);

        // Notifications (shared)
        Route::prefix('notifications')->group(function () {
            Route::get('/',          [NotificationController::class, 'index']);
            Route::patch('/{id}/read',[NotificationController::class, 'markRead']);
            Route::post('/mark-all', [NotificationController::class, 'markAllRead']);
        });

        // ── PATIENT routes ────────────────────────────────────────────────
        Route::middleware('role:patient')->prefix('patient')->group(function () {

            // Profile
            Route::get('/profile',        [PatientController::class, 'profile']);
            Route::put('/profile',        [PatientController::class, 'update']);

            // Appointments
            Route::prefix('appointments')->group(function () {
                Route::get('/',           [AppointmentController::class, 'patientIndex']);
                Route::post('/',          [AppointmentController::class, 'store']);
                Route::get('/{appointment}',    [AppointmentController::class, 'show']);
                Route::patch('/{appointment}/cancel', [AppointmentController::class, 'cancel']);
                Route::patch('/{appointment}/reschedule', [AppointmentController::class, 'reschedule']);
            });

            // Slot locking (optimistic UI)
            Route::post('/slots/lock',    [SlotController::class, 'lock']);
            Route::delete('/slots/lock',  [SlotController::class, 'unlock']);

            // Reviews
            Route::post('/reviews',       [ReviewController::class, 'store']);
        });

        // ── DOCTOR routes ─────────────────────────────────────────────────
        Route::middleware('role:doctor')->prefix('doctor')->group(function () {

            // Profile & availability
            Route::get('/profile',             [DoctorController::class, 'ownProfile']);
            Route::put('/profile',             [DoctorController::class, 'updateProfile']);
            Route::get('/availability',        [DoctorController::class, 'availability']);
            Route::put('/availability',        [DoctorController::class, 'updateAvailability']);
            Route::post('/exceptions',         [DoctorController::class, 'addException']);
            Route::delete('/exceptions/{id}',  [DoctorController::class, 'removeException']);

            // Appointments
            Route::prefix('appointments')->group(function () {
                Route::get('/',               [AppointmentController::class, 'doctorIndex']);
                Route::get('/today',          [AppointmentController::class, 'today']);
                Route::get('/pending',        [AppointmentController::class, 'pending']);
                Route::patch('/{appointment}/accept',  [AppointmentController::class, 'accept']);
                Route::patch('/{appointment}/decline', [AppointmentController::class, 'decline']);
                Route::patch('/{appointment}/complete',[AppointmentController::class, 'complete']);
                Route::patch('/{appointment}/notes',   [AppointmentController::class, 'addNotes']);
            });

            // Stats
            Route::get('/stats',               [DoctorController::class, 'stats']);
        });

        // ── ADMIN routes ──────────────────────────────────────────────────
        Route::middleware('role:admin')->prefix('admin')->group(function () {

            // Dashboard
            Route::get('/overview',            [AdminController::class, 'overview']);
            Route::get('/activity',            [AdminController::class, 'activity']);

            // Doctor management
            Route::prefix('doctors')->group(function () {
                Route::get('/',                [AdminController::class, 'listDoctors']);
                Route::get('/{doctor}',        [AdminController::class, 'doctorDetail']);
            });

            // Verification
            Route::prefix('verifications')->group(function () {
                Route::get('/',                [VerificationController::class, 'index']);
                Route::patch('/{doctor}/approve', [VerificationController::class, 'approve']);
                Route::patch('/{doctor}/reject',  [VerificationController::class, 'reject']);
            });

            // Appointments
            Route::get('/appointments',        [AdminController::class, 'appointments']);

            // Patients
            Route::get('/patients',            [AdminController::class, 'patients']);
        });
    });
});

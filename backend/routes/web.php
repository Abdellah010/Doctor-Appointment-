<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\PatientDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => Inertia::render('Landing'))->name('home');

Route::get('/login',                 [AuthController::class, 'showPatientLogin'])->name('login');
Route::get('/doctor-portal-secure',  [AuthController::class, 'showDoctorLogin'])->name('doctor.login');
Route::get('/admin-system-secure/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register',                [AuthController::class, 'showPatientRegister'])->name('register');
Route::get('/doctor-portal-secure/register', [AuthController::class, 'showDoctorRegister'])->name('doctor.register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

Route::get('/doctors',                 [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{doctor}',        [DoctorController::class, 'show'])->name('doctors.show');
Route::get('/doctors/{doctor}/slots',  [DoctorController::class, 'slots'])->name('doctors.slots');

/*
|--------------------------------------------------------------------------
| Patient routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');

    Route::post('/appointments',                        [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel',  [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
});

/*
|--------------------------------------------------------------------------
| Doctor routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:doctor', 'doctor.verified'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    Route::patch('/doctor/profile', [DoctorDashboardController::class, 'updateProfile'])->name('doctor.profile.update');
    Route::patch('/doctor/availability', [DoctorDashboardController::class, 'updateAvailability'])->name('doctor.availability.update');

    Route::patch('/appointments/{appointment}/accept',  [AppointmentController::class, 'accept'])->name('appointments.accept');
    Route::patch('/appointments/{appointment}/decline', [AppointmentController::class, 'decline'])->name('appointments.decline');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin-system-secure')->name('admin.')->group(function () {
    Route::get('/',                                    [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifications',                       [AdminController::class, 'verifications'])->name('verifications');
    Route::patch('/doctors/{doctor}/approve',          [AdminController::class, 'approve'])->name('doctors.approve');
    Route::patch('/doctors/{doctor}/reject',           [AdminController::class, 'reject'])->name('doctors.reject');
});

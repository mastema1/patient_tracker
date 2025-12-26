<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FileController;
use Illuminate\Auth\Middleware\Authenticate as AuthMiddleware;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'doctor'
            ? redirect()->route('doctor.dashboard')
            : redirect()->route('patient.dashboard');
    }
    return redirect()->route('login');
});

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware([AuthMiddleware::class])->group(function () {
    // Patient
    Route::middleware(['role:patient'])->group(function () {
        Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
        Route::get('/patient/seizures', [PatientController::class, 'seizures'])->name('patient.seizures');
        Route::get('/patient/seizures/new', [PatientController::class, 'createSeizure'])->name('patient.seizures.new');
        Route::post('/patient/seizures', [PatientController::class, 'storeSeizure'])->name('patient.seizures.store');
        Route::get('/patient/files', [PatientController::class, 'files'])->name('patient.files');
        Route::post('/patient/files/upload', [PatientController::class, 'uploadFile'])->name('patient.files.upload');
    });

    // Doctor
    Route::middleware(['role:doctor'])->group(function () {
        Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/doctor/patients', [DoctorController::class, 'patients'])->name('doctor.patients');
        Route::get('/doctor/patient/{id}', [DoctorController::class, 'patientReview'])->name('doctor.patient.review');
    });

    // File download (both roles)
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
});

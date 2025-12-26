<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DoctorFeedController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\PublicController;
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

// Global search (auth users)
Route::get('/search', [SearchController::class, 'index'])->middleware([AuthMiddleware::class])->name('search');

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

        // Patient new pages
        Route::get('/patient/history', [PatientController::class, 'history'])->name('patient.history');
        Route::get('/patient/profile', [PatientController::class, 'profile'])->name('patient.profile');
        Route::post('/patient/profile', [PatientController::class, 'updateProfile'])->name('patient.profile.update');
        Route::get('/patient/support', [PatientController::class, 'support'])->name('patient.support');
        Route::post('/patient/support', [PatientController::class, 'submitSupport'])->name('patient.support.submit');
        Route::post('/patient/feedback', [PatientController::class, 'submitFeedback'])->name('patient.feedback.submit');

        // Appointment request to doctor
        Route::post('/doctors/{id}/appointments', [AppointmentController::class, 'patientRequest'])->name('patient.appointments.request');
    });

    // Doctor
    Route::middleware(['role:doctor'])->group(function () {
        Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/doctor/patients', [DoctorController::class, 'patients'])->name('doctor.patients');
        Route::get('/doctor/patient/{id}', [DoctorController::class, 'patientReview'])->name('doctor.patient.review');

        // Clinical notes add
        Route::post('/doctor/patient/{id}/notes', [DoctorController::class, 'storeClinicalNote'])->name('doctor.patient.notes.store');

        // Medical Feed
        Route::get('/doctor/feed', [DoctorFeedController::class, 'index'])->name('doctor.feed');
        Route::post('/doctor/feed', [DoctorFeedController::class, 'storePost'])->name('doctor.feed.post');
        Route::post('/doctor/feed/{post}/comment', [DoctorFeedController::class, 'storeComment'])->name('doctor.feed.comment');

        // Doctor to patient appointment request
        Route::post('/doctor/patient/{id}/appointments', [AppointmentController::class, 'doctorRequest'])->name('doctor.appointments.request');
    });

    // Public doctor profile (both roles)
    Route::get('/doctors/{id}', [PublicController::class, 'doctorProfile'])->name('public.doctor.profile');

    // File download (both roles)
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');

    // Conversations (both roles)
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations/start', [ConversationController::class, 'start'])->name('conversations.start');
    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{id}/message', [ConversationController::class, 'send'])->name('conversations.message');
    Route::get('/messages/{id}/download', [ConversationController::class, 'download'])->name('conversations.download');
});

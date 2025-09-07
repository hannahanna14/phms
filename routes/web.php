<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PupilHealthController;
use App\Http\Controllers\HealthTreatmentController;
use App\Http\Controllers\OralHealthTreatmentController;
use App\Http\Controllers\HealthReportController;
use App\Http\Controllers\OralHealthReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/register', 'Auth/Register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::inertia('/login', 'Auth/Login')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

// Authenticated Routes Group
Route::middleware(['auth'])->group(function () {
    //Dashboard
    Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');

    // Logout Route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    //Pupil Health Routes
    Route::inertia('/pupil-health', 'Pupil Health/Index')->name('pupil-health');
    Route::get('/pupil-health', [StudentController::class, 'pupilHealth'])->name('pupil-health.index');
    Route::get('/pupil-health/record-type', [PupilHealthController::class, 'recordType'])->name('pupil-health.record-type');


    //Pupil Health Examination Routes
    Route::get('/pupil-health/health-examination/{student}/create', [PupilHealthController::class, 'createHealthExam'])
        ->name('health-examination.create');
    Route::post('/pupil-health/health-examination', [PupilHealthController::class, 'storeHealthExam'])
        ->name('health-examination.store');
    Route::get('/pupil-health/health-examination/{student}', [PupilHealthController::class, 'showHealthExam'])
        ->name('pupil-health.health-exam.show');

    Route::get('/pupil-health/oral-health/{student}', [PupilHealthController::class, 'showOralHealth'])
        ->name('pupil-health.oral-health.show');
    Route::get('/pupil-health/oral-health/{student}/create', [PupilHealthController::class, 'createOralHealth'])
        ->name('oral-health-examination.create');
    Route::post('/pupil-health/oral-health/store', [PupilHealthController::class, 'storeOralHealth'])
        ->name('oral-health-examination.store');
    Route::get('/pupil-health/incident/{student}', [StudentController::class, 'showIncident'])
        ->name('pupil-health.incident');
    Route::get('/pupil-health/incident/{student}/create', [StudentController::class, 'createIncident'])
        ->name('incident.create');
    Route::post('/pupil-health/incident', [StudentController::class, 'storeIncident'])
        ->name('incident.store');

    // Health Treatment Routes
    Route::get('/pupil-health/health-treatment/{student}/create', [HealthTreatmentController::class, 'create'])
        ->name('health-treatment.create');
    Route::post('/health-treatment', [HealthTreatmentController::class, 'store'])
        ->name('health-treatment.store');
    Route::get('/api/health-treatment/student/{student}', [HealthTreatmentController::class, 'index'])
        ->name('health-treatment.index');
    Route::put('/api/health-treatment/{healthTreatment}', [HealthTreatmentController::class, 'update'])
        ->name('health-treatment.update');

    // Oral Health Treatment Routes
    Route::get('/pupil-health/oral-health-treatment/{student}/create', [OralHealthTreatmentController::class, 'create'])
        ->name('oral-health-treatment.create');
    Route::post('/oral-health-treatment', [OralHealthTreatmentController::class, 'store'])
        ->name('oral-health-treatment.store');
    Route::get('/api/oral-health-treatment/student/{student}', [OralHealthTreatmentController::class, 'index'])
        ->name('oral-health-treatment.index');

    // Health Report Routes
    Route::get('/health-report', [HealthReportController::class, 'index'])
        ->name('health-report.index');
    Route::match(['GET', 'POST'], '/api/health-report/generate', [HealthReportController::class, 'generate'])
        ->name('health-report.generate');
    Route::post('/health-report/export-pdf', [HealthReportController::class, 'exportPdf'])
        ->name('health-report.export-pdf');
    Route::get('/health-report/preview-pdf', [HealthReportController::class, 'previewPdf'])
        ->name('health-report.preview-pdf');

    // Oral Health Report Routes
    Route::get('/oral-health-report', [OralHealthReportController::class, 'index'])
        ->name('oral-health-report.index');
    Route::match(['GET', 'POST'], '/oral-health-report/generate', [OralHealthReportController::class, 'generate'])
        ->name('oral-health-report.generate');
    Route::post('/oral-health-report/export-pdf', [OralHealthReportController::class, 'exportPdf'])
        ->name('oral-health-report.export-pdf');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/show', [UserController::class, 'show'])->name('users.show');
});

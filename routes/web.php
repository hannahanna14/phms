<?php

//new
use App\Http\Controllers\AuthController;

//old
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\PupilHealthController;
//use App\Http\Controllers\IncidentController;
//use App\Http\Controllers\OralHealthExaminationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
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

    //Generate Record Routes
    Route::get('/generate-record', [StudentController::class, 'generateRecord'])->name('generate-record');

    //Pupil Health Examination Routes
    Route::get('/pupil-health/health-examination/create', [PupilHealthController::class, 'createHealthExam'])
        ->name('health-examination.create');
    Route::post('/pupil-health/health-examination', [PupilHealthController::class, 'storeHealthExam'])
        ->name('health-examination.store');
    Route::get('/pupil-health/health-examination/{student}', [PupilHealthController::class, 'showHealthExam'])
        ->name('pupil-health.health-exam.show');

    Route::get('/pupil-health/oral-health/{student}', [PupilHealthController::class, 'showOralHealth'])
        ->name('pupil-health.oral-health.show');
    Route::get('/pupil-health/incident/{student}', [StudentController::class, 'showIncident'])
        ->name('pupil-health.incident');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/show', [UserController::class, 'show'])->name('users.show');
});

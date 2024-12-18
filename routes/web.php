<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\OralHealthExaminationController;
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

// Health Examination Routes
Route::get('/health-examination', [StudentController::class, 'index'])->name('health-examination.index');
Route::get('/health-examination/{student}', [StudentController::class, 'show'])->name('health-examination.show');
Route::get('/health-examination/{student}/create', [StudentController::class, 'create'])->name('health-examination.create');
Route::post('/health-examination', [StudentController::class, 'store'])->name('health-examination.store');
Route::get('/health-examination/{healthExamination}/edit', [StudentController::class, 'edit'])->name('health-examination.edit');
Route::put('/health-examination/{healthExamination}', [StudentController::class, 'update'])->name('health-examination.update');
Route::delete('/health-examination/{healthExamination}', [StudentController::class, 'destroy'])->name('health-examination.destroy');

// Oral Health Examination Routes
Route::get('/oral-health-examination', [OralHealthExaminationController::class, 'index'])->name('oral-health-examination.index');
Route::get('/oral-health-examination/{student}', [OralHealthExaminationController::class, 'show'])->name('oral-health-examination.show');

// Incident Routes
Route::get('/incident', [IncidentController::class, 'index'])->name('incident.index');
Route::get('/incident/{student}', [IncidentController::class, 'show'])->name('incident.show');

// User Management Routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{user}/show', [UserController::class, 'show'])->name('users.show');
Route::get('/incident/{student}', [IncidentController::class, 'show'])->name('incident.show');

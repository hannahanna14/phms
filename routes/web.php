<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\OralHealthExaminationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/register', 'Auth/Register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::inertia('/login', 'Auth/Login')->name('login');
Route::post('/login', [AuthController::class, 'login']);

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
Route::get('/oral-health-examination/{student}/create', [OralHealthExaminationController::class, 'create'])->name('oral-health-examination.create');
Route::post('/oral-health-examination', [OralHealthExaminationController::class, 'store'])->name('oral-health-examination.store');
Route::get('/oral-health-examination/{oralHealthExamination}/edit', [OralHealthExaminationController::class, 'edit'])->name('oral-health-examination.edit');
Route::put('/oral-health-examination/{oralHealthExamination}', [OralHealthExaminationController::class, 'update'])->name('oral-health-examination.update');
Route::delete('/oral-health-examination/{oralHealthExamination}', [OralHealthExaminationController::class, 'destroy'])->name('oral-health-examination.destroy');

// Incident Routes
Route::get('/incident', [IncidentController::class, 'index'])->name('incident.index');
Route::get('/incident/{student}', [IncidentController::class, 'show'])->name('incident.show');

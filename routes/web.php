<?php

use App\Http\Controllers\AuthController;
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

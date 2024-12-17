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
Route::get('/health-examination', [StudentController::class, 'index'])->name('health.examination');
Route::post('/health-examination', [StudentController::class, 'storeHealthExamination'])->name('health.examination.store');

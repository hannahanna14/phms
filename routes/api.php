<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PupilHealthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);

// Health Examination API Routes
Route::middleware('web')->group(function () {
    Route::get('/health-examination/{id}', [PupilHealthController::class, 'getHealthExamination']);
    Route::get('/health-examination/student/{studentId}', [PupilHealthController::class, 'getHealthExaminationByGradeYear']);
    Route::get('/oral-health-examination/student/{studentId}', [PupilHealthController::class, 'getOralHealthByGrade']);
});

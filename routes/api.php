<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PupilHealthController;
use App\Http\Controllers\HealthReportController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);

// Health Examination API Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/health-examination/{id}', [PupilHealthController::class, 'getHealthExamination']);
    Route::get('/health-examination/student/{studentId}', [PupilHealthController::class, 'getHealthExaminationByGradeYear']);
    Route::get('/oral-health-examination/student/{studentId}', [PupilHealthController::class, 'getOralHealthByGrade']);
    
    // Health Report API Routes
    Route::get('/students/search', [HealthReportController::class, 'searchStudents']);
    Route::post('/health-report/generate', [HealthReportController::class, 'generate']);
    Route::post('/health-report/export-pdf', [HealthReportController::class, 'exportPdf']);
    
    // Incident API Routes
    Route::get('/incidents/student/{studentId}', [StudentController::class, 'getIncidentsByStudent']);
    Route::put('/incidents/{id}/timer-status', [StudentController::class, 'updateTimerStatus']);
});

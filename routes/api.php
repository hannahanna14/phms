<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\HealthTreatmentController;
use App\Http\Controllers\OralHealthTreatmentController;
use App\Http\Controllers\HealthReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PupilHealthController;
use App\Http\Controllers\ConsultationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/students', [StudentController::class, 'store']);

// Health Examination API Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/health-examination/student/{studentId}', [PupilHealthController::class, 'getHealthExaminationByGradeYear']);
    
    // Debug route please huhu pasagdi ni don't mind:)
    Route::get('/debug/health-examination/{studentId}', function($studentId, Request $request) {
        try {
            $gradeLevel = $request->query('grade_level');
            
            return response()->json([
                'debug' => 'API call received',
                'student_id' => $studentId,
                'grade_level' => $gradeLevel,
                'query_params' => $request->query(),
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
    Route::get('/oral-health-examination/student/{studentId}', [PupilHealthController::class, 'getOralHealthByGrade']);
    
    // Health Report API Routes
    Route::get('/students/search', [HealthReportController::class, 'searchStudents']);
    Route::post('/health-report/generate', [HealthReportController::class, 'generate']);
    
    // Users API Routes
    Route::get('/users', function() {
        return response()->json(\App\Models\User::select('id', 'full_name', 'role')->get());
    });
    
    // Incident API Routes
    Route::get('/incidents/student/{studentId}', [StudentController::class, 'getIncidentsByStudent']);
    Route::put('/incidents/{id}/timer-status', [StudentController::class, 'updateTimerStatus']);
    
    // Notification API Routes
    Route::get('/notifications/check-timers', [NotificationController::class, 'checkTimerNotifications']);
    Route::get('/notifications/check-unrecorded', [NotificationController::class, 'checkUnrecordedStudents']);
    Route::get('/notifications/check-schedules', [NotificationController::class, 'checkScheduleNotifications']);
    
    // Timer Status API Routes for notifications
    Route::get('/health-treatment/timer-status/{id}', [HealthTreatmentController::class, 'getTimerStatus']);
    Route::get('/oral-health-treatment/timer-status/{id}', [OralHealthTreatmentController::class, 'getTimerStatus']);
    Route::get('/incidents/timer-status/{id}', [StudentController::class, 'getIncidentTimerStatus']);
    
    // Timer Control API Routes
    Route::post('/health-treatment/{healthTreatment}/start-timer', [HealthTreatmentController::class, 'startTimer']);
    Route::post('/health-treatment/{healthTreatment}/pause-timer', [HealthTreatmentController::class, 'pauseTimer']);
    Route::post('/health-treatment/{healthTreatment}/resume-timer', [HealthTreatmentController::class, 'resumeTimer']);
    Route::post('/health-treatment/{healthTreatment}/complete-timer', [HealthTreatmentController::class, 'completeTimer']);
    
    Route::post('/oral-health-treatment/{oralHealthTreatment}/start-timer', [OralHealthTreatmentController::class, 'startTimer']);
    Route::post('/oral-health-treatment/{oralHealthTreatment}/pause-timer', [OralHealthTreatmentController::class, 'pauseTimer']);
    Route::post('/oral-health-treatment/{oralHealthTreatment}/resume-timer', [OralHealthTreatmentController::class, 'resumeTimer']);
    Route::post('/oral-health-treatment/{oralHealthTreatment}/complete-timer', [OralHealthTreatmentController::class, 'completeTimer']);
    
    // Incident Timer Control API Routes
    Route::post('/incidents/{incident}/start-timer', [StudentController::class, 'startIncidentTimer']);
    Route::post('/incidents/{incident}/pause-timer', [StudentController::class, 'pauseIncidentTimer']);
    Route::post('/incidents/{incident}/resume-timer', [StudentController::class, 'resumeIncidentTimer']);
    Route::post('/incidents/{incident}/complete-timer', [StudentController::class, 'completeIncidentTimer']);
    
    // Consultation API Routes
    Route::get('/consultation/messages/{conversation}', [ConsultationController::class, 'getMessages']);
    Route::post('/consultation/send', [ConsultationController::class, 'sendMessage']);
    Route::post('/consultation/{conversation}/read', [ConsultationController::class, 'markAsRead']);
});

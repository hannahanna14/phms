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

Route::inertia('/login', 'Auth/Login')->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf-token');

// Authenticated Routes Group
Route::middleware(['auth'])->group(function () {
    //Dashboard
    Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/api/dashboard-data', [StudentController::class, 'dashboard'])->name('dashboard.api');
    Route::get('/api/dashboard/students-by-criteria', [StudentController::class, 'getStudentsByCriteria'])->name('dashboard.students-by-criteria');

    // Logout Route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    //Pupil Health Routes
    Route::get('/pupil-health', [StudentController::class, 'pupilHealth'])->name('pupil-health');
    Route::get('/pupil-health/record-type', [PupilHealthController::class, 'recordType'])->name('pupil-health.record-type');


    //Pupil Health Examination Routes
    Route::get('/pupil-health/health-examination/{student}/create', [PupilHealthController::class, 'createHealthExam'])
        ->name('health-examination.create');
    Route::post('/pupil-health/health-examination', [PupilHealthController::class, 'storeHealthExam'])
        ->name('health-examination.store');
    Route::get('/pupil-health/health-examination/{healthExamination}/edit', [PupilHealthController::class, 'editHealthExam'])
        ->name('health-examination.edit');
    Route::put('/pupil-health/health-examination/{healthExamination}', [PupilHealthController::class, 'updateHealthExam'])
        ->name('health-examination.update');
    Route::get('/pupil-health/health-examination/{student}', [PupilHealthController::class, 'showHealthExam'])
        ->name('pupil-health.health-exam.show');
    Route::get('/api/health-examination/student/{student}/all', [PupilHealthController::class, 'getAllHealthExams'])
        ->name('health-examination.all');

    Route::get('/pupil-health/oral-health/{student}', [PupilHealthController::class, 'showOralHealth'])
        ->name('pupil-health.oral-health.show');
    Route::get('/pupil-health/oral-health/{student}/create', [PupilHealthController::class, 'createOralHealth'])
        ->name('oral-health-examination.create');
    Route::post('/pupil-health/oral-health/store', [PupilHealthController::class, 'storeOralHealth'])
        ->name('oral-health-examination.store');
    Route::get('/pupil-health/oral-health/{id}/edit', [PupilHealthController::class, 'editOralHealth'])
        ->name('oral-health-examination.edit');
    Route::put('/pupil-health/oral-health/{oralHealthExamination}', [PupilHealthController::class, 'updateOralHealth'])
        ->name('oral-health-examination.update');
    Route::get('/pupil-health/incident/{student}', [StudentController::class, 'showIncident'])
        ->name('pupil-health.incident');
    Route::get('/pupil-health/incident/{student}/create', [StudentController::class, 'createIncident'])
        ->name('incident.create');
    Route::post('/pupil-health/incident', [StudentController::class, 'storeIncident'])
        ->name('incident.store');
    Route::get('/pupil-health/incident/{incident}/view', [StudentController::class, 'viewIncident'])
        ->name('incident.view');
    Route::get('/pupil-health/incident/{incident}/edit', [StudentController::class, 'editIncident'])
        ->name('incident.edit');
    Route::put('/pupil-health/incident/{incident}', [StudentController::class, 'updateIncident'])
        ->name('incident.update');

    // Health Treatment Routes
    Route::get('/pupil-health/health-treatment/{student}/create', [HealthTreatmentController::class, 'create'])
        ->name('health-treatment.create');
    Route::post('/health-treatment', [HealthTreatmentController::class, 'store'])
        ->name('health-treatment.store');
    Route::get('/api/health-treatment/student/{student}', [HealthTreatmentController::class, 'index'])
        ->name('health-treatment.index');
    Route::get('/health-treatment/{healthTreatment}/edit', [HealthTreatmentController::class, 'edit'])
        ->name('health-treatment.edit');
    Route::get('/health-treatment/{healthTreatment}', [HealthTreatmentController::class, 'show'])
        ->name('health-treatment.show');
    Route::put('/health-treatment/{healthTreatment}', [HealthTreatmentController::class, 'update'])
        ->name('health-treatment.update');
    Route::get('/api/health-treatment/{healthTreatment}/timer-status', [HealthTreatmentController::class, 'getTimerStatus'])
        ->name('health-treatment.timer-status');

    // Oral Health Treatment Routes
    Route::get('/pupil-health/oral-health-treatment/{student}/create', [OralHealthTreatmentController::class, 'create'])
        ->name('oral-health-treatment.create');
    Route::post('/oral-health-treatment', [OralHealthTreatmentController::class, 'store'])
        ->name('oral-health-treatment.store');
    Route::get('/api/oral-health-treatment/student/{student}', [OralHealthTreatmentController::class, 'index'])
        ->name('oral-health-treatment.index');
    Route::get('/oral-health-treatment/{oralHealthTreatment}/edit', [OralHealthTreatmentController::class, 'edit'])
        ->name('oral-health-treatment.edit');
    Route::get('/oral-health-treatment/{oralHealthTreatment}', [OralHealthTreatmentController::class, 'show'])
        ->name('oral-health-treatment.show');
    Route::put('/oral-health-treatment/{oralHealthTreatment}', [OralHealthTreatmentController::class, 'update'])
        ->name('oral-health-treatment.update');

    // Health Report Routes (Admin and Nurse only)
    Route::middleware(['role:admin,nurse'])->group(function () {
        Route::get('/health-report', [HealthReportController::class, 'index'])
            ->name('health-report.index');
        Route::match(['GET', 'POST'], '/health-report/generate', [HealthReportController::class, 'generate'])
            ->name('health-report.generate');
        Route::match(['GET', 'POST'], '/health-report/export-pdf', [HealthReportController::class, 'exportPdf'])
            ->name('health-report.export-pdf');
        Route::post('/health-report/export-pdf/queued', [HealthReportController::class, 'exportPdfQueued'])->name('health-report.export-pdf.queued');
        Route::get('/health-report/export-pdf/status/{exportId}', [HealthReportController::class, 'exportPdfStatus'])->name('health-report.export-pdf.status');
        Route::get('/health-report/export-pdf/download/{exportId}', [HealthReportController::class, 'exportPdfDownload'])->name('health-report.export-pdf.download');
        Route::get('/health-report/preview-pdf', [HealthReportController::class, 'previewPdf'])
            ->name('health-report.preview-pdf');
        Route::get('/api/health-report/students/search', [HealthReportController::class, 'searchStudents'])
            ->name('health-report.search-students');
    });

    // Oral Health Report Routes (Admin and Nurse only)
    Route::middleware(['role:admin,nurse'])->group(function () {
        Route::get('/oral-health-report', [OralHealthReportController::class, 'index'])->name('oral-health-report.index');
        Route::match(['GET', 'POST'], '/oral-health-report/generate', [OralHealthReportController::class, 'generate'])->name('oral-health-report.generate');
        Route::match(['GET', 'POST'], '/oral-health-report/export-pdf', [OralHealthReportController::class, 'exportPdf'])->name('oral-health-report.export-pdf');
        Route::get('/api/oral-health-report/students/search', [OralHealthReportController::class, 'searchStudents'])->name('oral-health-report.search-students');
    });

    // Health examination PDF routes
    Route::get('/health-examination-pdf/{studentId}', [App\Http\Controllers\HealthReportController::class, 'exportHealthExaminationPdf'])->name('health-examination.export-pdf');
    Route::get('/test-health-examination-pdf/{studentId?}', [App\Http\Controllers\HealthReportController::class, 'testHealthExaminationPdf'])->name('test.health-examination-pdf');
    
    // Oral health examination PDF routes
    Route::get('/oral-health-examination/{studentId}/pdf', [App\Http\Controllers\HealthReportController::class, 'exportOralHealthExaminationPdf'])->name('oral-health-examination.export-pdf');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/show', [UserController::class, 'show'])->name('users.show');
    
    // Schedule Calendar Routes
    Route::resource('schedule-calendar', App\Http\Controllers\ScheduleController::class);
    Route::get('/api/schedule/events', [App\Http\Controllers\ScheduleController::class, 'getEvents'])->name('schedule.events');
    
    // Consultation Routes (Modern Messaging)
    Route::get('/consultation', [App\Http\Controllers\ConsultationController::class, 'index'])->name('consultation.index');
    Route::post('/consultation/start', [App\Http\Controllers\ConsultationController::class, 'startConversation'])->name('consultation.start');
    Route::post('/consultation/send', [App\Http\Controllers\ConsultationController::class, 'sendMessage'])->name('consultation.send');
    Route::get('/consultation/{conversation}/messages', [App\Http\Controllers\ConsultationController::class, 'getMessages'])->name('consultation.messages');
    Route::post('/consultation/{conversation}/read', [App\Http\Controllers\ConsultationController::class, 'markAsRead'])->name('consultation.read');
    
    // Settings Routes (Admin Only)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.store');
    
    // Health Data Export Routes (Admin Only)
    Route::get('/health-data-export', [App\Http\Controllers\HealthDataExportController::class, 'index'])->name('health-data-export.index');
    Route::get('/health-data-export/health-examinations', [App\Http\Controllers\HealthDataExportController::class, 'exportHealthExaminations'])->name('health-data-export.health-examinations');
    Route::get('/health-data-export/oral-health-examinations', [App\Http\Controllers\HealthDataExportController::class, 'exportOralHealthExaminations'])->name('health-data-export.oral-health-examinations');
    Route::get('/health-data-export/health-treatments', [App\Http\Controllers\HealthDataExportController::class, 'exportHealthTreatments'])->name('health-data-export.health-treatments');
    Route::get('/health-data-export/oral-health-treatments', [App\Http\Controllers\HealthDataExportController::class, 'exportOralHealthTreatments'])->name('health-data-export.oral-health-treatments');
    Route::get('/health-data-export/incidents', [App\Http\Controllers\HealthDataExportController::class, 'exportIncidents'])->name('health-data-export.incidents');
    
    // Error Logs Routes (Admin Only)
    Route::get('/error-logs', [App\Http\Controllers\ErrorLogsController::class, 'index'])->name('error-logs.index');
    Route::post('/error-logs/clear', [App\Http\Controllers\ErrorLogsController::class, 'clearLogs'])->name('error-logs.clear');
    Route::get('/error-logs/download', [App\Http\Controllers\ErrorLogsController::class, 'downloadLogs'])->name('error-logs.download');
    
    // Notifications Routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    
    // Student Management Routes (Admin Only)
    Route::get('/student-management', [App\Http\Controllers\StudentManagementController::class, 'index'])->name('student-management.index');
    Route::post('/api/student-management/promote', [App\Http\Controllers\StudentManagementController::class, 'promoteStudents'])->name('student-management.promote');
    Route::put('/api/student-management/student/{student}', [App\Http\Controllers\StudentManagementController::class, 'updateStudent'])->name('student-management.update-student');
    Route::get('/api/student-management/students-by-grade', [App\Http\Controllers\StudentManagementController::class, 'getStudentsByGrade'])->name('student-management.students-by-grade');
    Route::post('/api/student-management/bulk-assign-teacher', [App\Http\Controllers\StudentManagementController::class, 'bulkAssignTeacher'])->name('student-management.bulk-assign-teacher');
});

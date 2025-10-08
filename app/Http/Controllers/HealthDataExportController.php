<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HealthExaminationsExport;
use App\Exports\OralHealthExaminationsExport;
use App\Exports\HealthTreatmentsExport;
use App\Exports\OralHealthTreatmentsExport;
use App\Exports\IncidentsExport;
use App\Models\SchoolSettings;

class HealthDataExportController extends Controller
{
    /**
     * Display the health data export page
     */
    public function index()
    {
        // Only admins can access health data export
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $schoolSettings = SchoolSettings::getInstance();

        return Inertia::render('HealthDataExport/Index', [
            'schoolSettings' => $schoolSettings
        ]);
    }

    /**
     * Export health examinations data
     */
    public function exportHealthExaminations(Request $request)
    {
        // Only admins can export data
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $validated = $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'include_personal_info' => 'boolean'
        ]);

        // Get health examinations data
        $query = \App\Models\HealthExamination::with('student')
            ->orderBy('examination_date', 'desc');

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('examination_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('examination_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level'])) {
            $gradeLevel = $validated['grade_level'];
            $query->where(function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', "Grade {$gradeLevel}");
            });
        }

        $examinations = $query->get();

        // Log the export activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'export_type' => 'health_examinations',
                'format' => 'csv',
                'filters' => $validated,
                'record_count' => $examinations->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Exported health examinations data');

        // Create CSV content
        $filename = 'health-examinations-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($examinations, $validated) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            $csvHeaders = [];
            if ($validated['include_personal_info'] ?? true) {
                $csvHeaders = array_merge($csvHeaders, [
                    'Student Name', 'LRN', 'Grade Level', 'Section', 'Gender', 'Date of Birth'
                ]);
            }
            $csvHeaders = array_merge($csvHeaders, [
                'Examination Date', 'School Year', 'Age', 'Weight (kg)', 'Height (cm)', 'BMI',
                'Nutritional Status (BMI)', 'Nutritional Status (Height)', 'Temperature', 'Blood Pressure',
                'Heart Rate', 'Pulse Rate', 'Respiratory Rate', 'Vision Screening', 'Auditory Screening',
                'Skin', 'Scalp', 'Eyes', 'Ears', 'Nose', 'Mouth', 'Neck', 'Throat', 'Chest', 'Back',
                'Heart', 'Abdomen', 'Deformities', 'Iron Supplementation', 'Deworming', 'Immunization',
                'SBFP Beneficiary', '4Ps Beneficiary', 'Menarche', 'Remarks'
            ]);
            
            fputcsv($file, $csvHeaders);

            // Add data rows
            foreach ($examinations as $examination) {
                $row = [];
                
                if ($validated['include_personal_info'] ?? true) {
                    $row = array_merge($row, [
                        $examination->student->full_name ?? 'N/A',
                        $examination->student->lrn ?? 'N/A',
                        $examination->student->grade_level ?? 'N/A',
                        $examination->student->section ?? 'N/A',
                        $examination->student->gender ?? 'N/A',
                        $examination->student->date_of_birth ? $examination->student->date_of_birth->format('Y-m-d') : 'N/A',
                    ]);
                }
                
                $row = array_merge($row, [
                    $examination->examination_date ? $examination->examination_date->format('Y-m-d') : 'N/A',
                    $examination->school_year ?? 'N/A',
                    $examination->age ?? 'N/A',
                    $examination->weight ?? 'N/A',
                    $examination->height ?? 'N/A',
                    $examination->bmi ?? 'N/A',
                    $examination->nutritional_status_bmi ?? 'N/A',
                    $examination->nutritional_status_height ?? 'N/A',
                    $examination->temperature ?? 'N/A',
                    $examination->blood_pressure ?? 'N/A',
                    $examination->heart_rate ?? 'N/A',
                    $examination->pulse_rate ?? 'N/A',
                    $examination->respiratory_rate ?? 'N/A',
                    $examination->vision_screening ?? 'N/A',
                    $examination->auditory_screening ?? 'N/A',
                    $examination->skin ?? 'N/A',
                    $examination->scalp ?? 'N/A',
                    $examination->eyes ?? 'N/A',
                    $examination->ears ?? 'N/A',
                    $examination->nose ?? 'N/A',
                    $examination->mouth ?? 'N/A',
                    $examination->neck ?? 'N/A',
                    $examination->throat ?? 'N/A',
                    $examination->chest ?? 'N/A',
                    $examination->back ?? 'N/A',
                    $examination->heart ?? 'N/A',
                    $examination->abdomen ?? 'N/A',
                    $examination->deformities ?? 'N/A',
                    $examination->iron_supplementation ?? 'N/A',
                    $examination->deworming ?? 'N/A',
                    $examination->immunization ?? 'N/A',
                    $examination->sbfp_beneficiary ? 'Yes' : 'No',
                    $examination->four_ps_beneficiary ? 'Yes' : 'No',
                    $examination->menarche ?? 'N/A',
                    $examination->remarks ?? 'N/A',
                ]);
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export oral health examinations data
     */
    public function exportOralHealthExaminations(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $validated = $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'include_personal_info' => 'boolean'
        ]);

        $filename = 'oral-health-examinations-' . date('Y-m-d-H-i-s') . '.' . $validated['format'];
        
        return Excel::download(
            new OralHealthExaminationsExport($validated), 
            $filename
        );
    }

    /**
     * Export health treatments data
     */
    public function exportHealthTreatments(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $validated = $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        $filename = 'health-treatments-' . date('Y-m-d-H-i-s') . '.' . $validated['format'];
        
        return Excel::download(
            new HealthTreatmentsExport($validated), 
            $filename
        );
    }

    /**
     * Export oral health treatments data
     */
    public function exportOralHealthTreatments(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $validated = $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        $filename = 'oral-health-treatments-' . date('Y-m-d-H-i-s') . '.' . $validated['format'];
        
        return Excel::download(
            new OralHealthTreatmentsExport($validated), 
            $filename
        );
    }

    /**
     * Export incidents data
     */
    public function exportIncidents(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can export health data.');
        }

        $validated = $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        $filename = 'incidents-' . date('Y-m-d-H-i-s') . '.' . $validated['format'];
        
        return Excel::download(
            new IncidentsExport($validated), 
            $filename
        );
    }
}

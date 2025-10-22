<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'include_personal_info' => 'boolean'
        ]);

        // Get health examinations data
        $query = \App\Models\HealthExamination::with('student')
            ->whereHas('student') // Only include records with existing students
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
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'include_personal_info' => 'boolean'
        ]);

        // Get oral health examinations data
        $query = \App\Models\OralHealthExamination::with('student')
            ->whereHas('student') // Only include records with existing students
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
                'export_type' => 'oral_health_examinations',
                'format' => 'csv',
                'filters' => $validated,
                'record_count' => $examinations->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Exported oral health examinations data');

        // Create CSV content
        $filename = 'oral-health-examinations-' . date('Y-m-d-H-i-s') . '.csv';
        
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
                'Examination Date', 'School Year',
                // Permanent Teeth
                'Permanent - Index DFT', 'Permanent - Teeth Decayed', 'Permanent - Teeth Filled',
                'Permanent - Total DFT', 'Permanent - For Extraction', 'Permanent - For Filling', 'Permanent - Missing',
                // Temporary Teeth
                'Temporary - Index DFT', 'Temporary - Teeth Decayed', 'Temporary - Teeth Filled',
                'Temporary - Total DFT', 'Temporary - For Extraction', 'Temporary - For Filling', 'Temporary - Missing',
                // Tooth Symbols and Conditions
                'Tooth Symbols', 'Oral Health Conditions', 'Remarks'
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
                
                // Format tooth symbols for export
                $toothSymbols = 'N/A';
                if ($examination->tooth_symbols && is_array($examination->tooth_symbols)) {
                    $symbols = [];
                    foreach ($examination->tooth_symbols as $tooth => $conditions) {
                        if (is_array($conditions)) {
                            $symbols[] = $tooth . ': ' . implode(', ', $conditions);
                        } else {
                            $symbols[] = $tooth . ': ' . $conditions;
                        }
                    }
                    $toothSymbols = implode(' | ', $symbols);
                }

                // Format conditions for export
                $conditions = 'N/A';
                if ($examination->conditions && is_array($examination->conditions)) {
                    $conditionList = [];
                    foreach ($examination->conditions as $condition => $date) {
                        $conditionList[] = $condition . ' (' . $date . ')';
                    }
                    $conditions = implode(' | ', $conditionList);
                }
                
                $row = array_merge($row, [
                    $examination->examination_date ? $examination->examination_date->format('Y-m-d') : 'N/A',
                    $examination->school_year ?? 'N/A',
                    // Permanent Teeth
                    $examination->permanent_index_dft ?? 'N/A',
                    $examination->permanent_teeth_decayed ?? 'N/A',
                    $examination->permanent_teeth_filled ?? 'N/A',
                    $examination->permanent_total_dft ?? 'N/A',
                    $examination->permanent_for_extraction ?? 'N/A',
                    $examination->permanent_for_filling ?? 'N/A',
                    $examination->permanent_teeth_missing ?? 'N/A',
                    // Temporary Teeth
                    $examination->temporary_index_dft ?? 'N/A',
                    $examination->temporary_teeth_decayed ?? 'N/A',
                    $examination->temporary_teeth_filled ?? 'N/A',
                    $examination->temporary_total_dft ?? 'N/A',
                    $examination->temporary_for_extraction ?? 'N/A',
                    $examination->temporary_for_filling ?? 'N/A',
                    $examination->temporary_teeth_missing ?? 'N/A',
                    // Tooth Symbols and Conditions
                    $toothSymbols,
                    $conditions,
                    $examination->remarks ?? 'N/A',
                ]);
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        // Get health treatments data
        $query = \App\Models\HealthTreatment::with('student')
            ->whereHas('student') // Only include records with existing students
            ->orderBy('date', 'desc');

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level'])) {
            $gradeLevel = $validated['grade_level'];
            $query->where(function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', "Grade {$gradeLevel}");
            });
        }

        $treatments = $query->get();

        // Log the export activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'export_type' => 'health_treatments',
                'format' => 'csv',
                'filters' => $validated,
                'record_count' => $treatments->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Exported health treatments data');

        // Create CSV content
        $filename = 'health-treatments-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($treatments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            $csvHeaders = [
                'Student Name', 'LRN', 'Grade Level', 'Section', 'Treatment Date', 'School Year',
                'Title', 'Chief Complaint', 'Treatment', 'Status', 'Remarks'
            ];
            
            fputcsv($file, $csvHeaders);

            // Add data rows
            foreach ($treatments as $treatment) {
                $row = [
                    $treatment->student->full_name ?? 'N/A',
                    $treatment->student->lrn ?? 'N/A',
                    $treatment->student->grade_level ?? 'N/A',
                    $treatment->student->section ?? 'N/A',
                    $treatment->date ? $treatment->date->format('Y-m-d') : 'N/A',
                    $treatment->school_year ?? 'N/A',
                    $treatment->title ?? 'N/A',
                    $treatment->chief_complaint ?? 'N/A',
                    $treatment->treatment ?? 'N/A',
                    $treatment->status ?? 'N/A',
                    $treatment->remarks ?? 'N/A',
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        // Get oral health treatments data
        $query = \App\Models\OralHealthTreatment::with('student')
            ->whereHas('student') // Only include records with existing students
            ->orderBy('date', 'desc');

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level'])) {
            $gradeLevel = $validated['grade_level'];
            $query->where(function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', "Grade {$gradeLevel}");
            });
        }

        $treatments = $query->get();

        // Log the export activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'export_type' => 'oral_health_treatments',
                'format' => 'csv',
                'filters' => $validated,
                'record_count' => $treatments->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Exported oral health treatments data');

        // Create CSV content
        $filename = 'oral-health-treatments-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($treatments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            $csvHeaders = [
                'Student Name', 'LRN', 'Grade Level', 'Section', 'Treatment Date', 'School Year',
                'Title', 'Chief Complaint', 'Treatment', 'Remarks', 'Timer Status'
            ];
            
            fputcsv($file, $csvHeaders);

            // Add data rows
            foreach ($treatments as $treatment) {
                $row = [
                    $treatment->student->full_name ?? 'N/A',
                    $treatment->student->lrn ?? 'N/A',
                    $treatment->student->grade_level ?? 'N/A',
                    $treatment->student->section ?? 'N/A',
                    $treatment->date ? $treatment->date->format('Y-m-d') : 'N/A',
                    $treatment->school_year ?? 'N/A',
                    $treatment->title ?? 'N/A',
                    $treatment->chief_complaint ?? 'N/A',
                    $treatment->treatment ?? 'N/A',
                    $treatment->remarks ?? 'N/A',
                    $treatment->timer_status ?? 'N/A',
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string'
        ]);

        // Get incidents data
        $query = \App\Models\Incident::with('student')
            ->whereHas('student') // Only include records with existing students
            ->orderBy('date', 'desc');

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level'])) {
            $gradeLevel = $validated['grade_level'];
            $query->where(function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', "Grade {$gradeLevel}");
            });
        }

        $incidents = $query->get();

        // Log the export activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'export_type' => 'incidents',
                'format' => 'csv',
                'filters' => $validated,
                'record_count' => $incidents->count(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('Exported incidents data');

        // Create CSV content
        $filename = 'incidents-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            $csvHeaders = [
                'Student Name', 'LRN', 'Grade Level', 'Section', 'Incident Date', 'School Year',
                'Complaint', 'Actions Taken', 'Status', 'Timer Status', 'Started At', 'Expires At', 'Is Expired'
            ];
            
            fputcsv($file, $csvHeaders);

            // Add data rows
            foreach ($incidents as $incident) {
                $row = [
                    $incident->student->full_name ?? 'N/A',
                    $incident->student->lrn ?? 'N/A',
                    $incident->student->grade_level ?? 'N/A',
                    $incident->student->section ?? 'N/A',
                    $incident->date ? $incident->date->format('Y-m-d') : 'N/A',
                    $incident->school_year ?? 'N/A',
                    $incident->complaint ?? 'N/A',
                    $incident->actions_taken ?? 'N/A',
                    $incident->status ?? 'N/A',
                    $incident->timer_status ?? 'N/A',
                    $incident->started_at ? $incident->started_at->format('Y-m-d H:i:s') : 'N/A',
                    $incident->expires_at ? $incident->expires_at->format('Y-m-d H:i:s') : 'N/A',
                    $incident->is_expired ? 'Yes' : 'No',
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

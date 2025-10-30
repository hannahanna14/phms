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
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $validated = $request->validate([
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'sort_by' => 'nullable|string|in:name_asc,name_desc,lrn_asc,lrn_desc,date_desc,date_asc,grade_date',
            'include_personal_info' => 'boolean'
        ]);

        // Get health examinations data
        $query = \App\Models\HealthExamination::with('student')
            ->whereHas('student'); // Only include records with existing students

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('examination_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('examination_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level']) && $validated['grade_level'] !== 'all') {
            $gradeLevel = $validated['grade_level'];
            $query->whereHas('student', function($q) use ($gradeLevel) {
                // Handle different grade level formats
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', 'LIKE', $gradeLevel . '%');
            });
        }

        // Apply sorting
        $sortBy = $validated['sort_by'] ?? 'name_asc';
        
        if ($sortBy === 'name_desc') {
            $examinations = $query->join('students', 'health_examinations.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'desc')
                ->select('health_examinations.*')
                ->get();
        } elseif ($sortBy === 'grade_date') {
            $examinations = $query->join('students', 'health_examinations.student_id', '=', 'students.id')
                ->orderByRaw("
                    CASE 
                        WHEN students.grade_level = 'Kinder 2' OR students.grade_level LIKE 'Kinder%' THEN 1
                        WHEN students.grade_level = 'Grade 1' OR students.grade_level = '1' THEN 2
                        WHEN students.grade_level = 'Grade 2' OR students.grade_level = '2' THEN 3
                        WHEN students.grade_level = 'Grade 3' OR students.grade_level = '3' THEN 4
                        WHEN students.grade_level = 'Grade 4' OR students.grade_level = '4' THEN 5
                        WHEN students.grade_level = 'Grade 5' OR students.grade_level = '5' THEN 6
                        WHEN students.grade_level = 'Grade 6' OR students.grade_level = '6' THEN 7
                        ELSE 99
                    END
                ")
                ->orderBy('health_examinations.examination_date', 'desc')
                ->select('health_examinations.*')
                ->get();
        } else {
            // Default: name_asc
            $examinations = $query->join('students', 'health_examinations.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'asc')
                ->select('health_examinations.*')
                ->get();
        }

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
                'Examination Date', 'School Year', 'Weight (kg)', 'Height (cm)',
                'Nutritional Status (BMI)', 'Nutritional Status (Height)', 'Temperature',
                'Heart Rate', 'Vision Screening', 'Auditory Screening',
                'Skin', 'Scalp', 'Eye', 'Ear', 'Nose', 'Mouth', 'Neck', 'Throat',
                'Lungs', 'Heart', 'Abdomen', 'Deformities', 'Iron Supplementation', 'Deworming Status',
                'SBFP Beneficiary', '4Ps Beneficiary', 'Remarks'
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
                        $examination->student->sex ?? 'N/A',
                        $examination->student->date_of_birth ? $examination->student->date_of_birth->format('Y-m-d') : 'N/A',
                    ]);
                }
                
                // Helper function to get field value with specify support
                $getFieldValue = function($field, $specifyField) use ($examination) {
                    $value = $examination->$field ?? '';
                    
                    // Check if value is "Others (specify)" or similar variations
                    if (stripos($value, 'others') !== false && stripos($value, 'specify') !== false) {
                        if (!empty($examination->$specifyField)) {
                            return $examination->$specifyField;
                        }
                    }
                    
                    return $value ?: 'N/A';
                };
                
                // Get lungs value (separate from heart)
                $lungsValue = $examination->lungs ?? '';
                if (stripos($lungsValue, 'others') !== false && stripos($lungsValue, 'specify') !== false) {
                    $lungsValue = $examination->lungs_specify ?? $examination->lungs_other_specify ?? $lungsValue;
                }
                $lungsValue = $lungsValue ?: 'N/A';
                
                // Get heart value (separate from lungs)
                $heartValue = $examination->heart ?? '';
                if (stripos($heartValue, 'others') !== false && stripos($heartValue, 'specify') !== false) {
                    $heartValue = $examination->heart_specify ?? $examination->heart_other_specify ?? $heartValue;
                }
                $heartValue = $heartValue ?: 'N/A';
                
                $row = array_merge($row, [
                    $examination->examination_date ? $examination->examination_date->format('Y-m-d') : 'N/A',
                    $examination->school_year ?? 'N/A',
                    $examination->weight ?? 'N/A',
                    $examination->height ?? 'N/A',
                    $examination->nutritional_status_bmi ?? 'N/A',
                    $examination->nutritional_status_height ?? 'N/A',
                    $examination->temperature ?? 'N/A',
                    $examination->heart_rate ?? 'N/A',
                    $getFieldValue('vision_screening', 'vision_screening_specify'),
                    $getFieldValue('auditory_screening', 'auditory_screening_specify'),
                    $getFieldValue('skin', 'skin_specify'),
                    $getFieldValue('scalp', 'scalp_specify'),
                    $getFieldValue('eye', 'eye_specify'),
                    $getFieldValue('ear', 'ear_specify'),
                    $getFieldValue('nose', 'nose_specify'),
                    $getFieldValue('mouth', 'mouth_specify'),
                    $getFieldValue('neck', 'neck_specify'),
                    $getFieldValue('throat', 'throat_specify'),
                    $lungsValue,
                    $heartValue,
                    $getFieldValue('abdomen', 'abdomen_specify'),
                    $getFieldValue('deformities', 'deformities_specify'),
                    $examination->iron_supplementation ?? 'N/A',
                    $examination->deworming_status ?? 'N/A',
                    $examination->sbfp_beneficiary ? 'Yes' : 'No',
                    $examination->four_ps_beneficiary ? 'Yes' : 'No',
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

        // Validate request
        $validated = $request->validate([
            'format' => 'required|in:csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'grade_level' => 'nullable|string',
            'sort_by' => 'nullable|string|in:name_asc,name_desc,grade_date',
            'include_personal_info' => 'boolean'
        ]);

        // Get oral health examinations data
        $query = \App\Models\OralHealthExamination::with('student')
            ->whereHas('student'); // Only include records with existing students

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('examination_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('examination_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level']) && $validated['grade_level'] !== 'all') {
            $gradeLevel = $validated['grade_level'];
            $query->whereHas('student', function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', 'LIKE', $gradeLevel . '%');
            });
        }

        // Apply sorting
        $sortBy = $validated['sort_by'] ?? 'name_asc';
        
        if ($sortBy === 'name_desc') {
            $examinations = $query->join('students', 'oral_health_examinations.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'desc')
                ->select('oral_health_examinations.*')
                ->get();
        } elseif ($sortBy === 'grade_date') {
            $examinations = $query->join('students', 'oral_health_examinations.student_id', '=', 'students.id')
                ->orderByRaw("
                    CASE 
                        WHEN students.grade_level = 'Kinder 2' OR students.grade_level LIKE 'Kinder%' THEN 1
                        WHEN students.grade_level = 'Grade 1' OR students.grade_level = '1' THEN 2
                        WHEN students.grade_level = 'Grade 2' OR students.grade_level = '2' THEN 3
                        WHEN students.grade_level = 'Grade 3' OR students.grade_level = '3' THEN 4
                        WHEN students.grade_level = 'Grade 4' OR students.grade_level = '4' THEN 5
                        WHEN students.grade_level = 'Grade 5' OR students.grade_level = '5' THEN 6
                        WHEN students.grade_level = 'Grade 6' OR students.grade_level = '6' THEN 7
                        ELSE 99
                    END
                ")
                ->orderBy('oral_health_examinations.examination_date', 'desc')
                ->select('oral_health_examinations.*')
                ->get();
        } else {
            // Default: name_asc
            $examinations = $query->join('students', 'oral_health_examinations.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'asc')
                ->select('oral_health_examinations.*')
                ->get();
        }

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
                        $examination->student->sex ?? 'N/A',
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
            'grade_level' => 'nullable|string',
            'sort_by' => 'nullable|string|in:name_asc,name_desc,grade_date'
        ]);

        // Get health treatments data
        $query = \App\Models\HealthTreatment::with('student')
            ->whereHas('student'); // Only include records with existing students

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level']) && $validated['grade_level'] !== 'all') {
            $gradeLevel = $validated['grade_level'];
            $query->whereHas('student', function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', 'LIKE', $gradeLevel . '%');
            });
        }

        // Apply sorting
        $sortBy = $validated['sort_by'] ?? 'name_asc';
        
        if ($sortBy === 'name_desc') {
            $treatments = $query->join('students', 'health_treatments.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'desc')
                ->select('health_treatments.*')
                ->get();
        } elseif ($sortBy === 'grade_date') {
            $treatments = $query->join('students', 'health_treatments.student_id', '=', 'students.id')
                ->orderByRaw("
                    CASE 
                        WHEN students.grade_level = 'Kinder 2' OR students.grade_level LIKE 'Kinder%' THEN 1
                        WHEN students.grade_level = 'Grade 1' OR students.grade_level = '1' THEN 2
                        WHEN students.grade_level = 'Grade 2' OR students.grade_level = '2' THEN 3
                        WHEN students.grade_level = 'Grade 3' OR students.grade_level = '3' THEN 4
                        WHEN students.grade_level = 'Grade 4' OR students.grade_level = '4' THEN 5
                        WHEN students.grade_level = 'Grade 5' OR students.grade_level = '5' THEN 6
                        WHEN students.grade_level = 'Grade 6' OR students.grade_level = '6' THEN 7
                        ELSE 99
                    END
                ")
                ->orderBy('health_treatments.date', 'desc')
                ->select('health_treatments.*')
                ->get();
        } else {
            // Default: name_asc
            $treatments = $query->join('students', 'health_treatments.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'asc')
                ->select('health_treatments.*')
                ->get();
        }

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
            'grade_level' => 'nullable|string',
            'sort_by' => 'nullable|string|in:name_asc,name_desc,grade_date'
        ]);

        // Get oral health treatments data
        $query = \App\Models\OralHealthTreatment::with('student')
            ->whereHas('student'); // Only include records with existing students

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        if (!empty($validated['grade_level']) && $validated['grade_level'] !== 'all') {
            $gradeLevel = $validated['grade_level'];
            $query->whereHas('student', function($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel)
                  ->orWhere('grade_level', 'LIKE', $gradeLevel . '%');
            });
        }

        // Apply sorting
        $sortBy = $validated['sort_by'] ?? 'name_asc';
        
        if ($sortBy === 'name_desc') {
            $treatments = $query->join('students', 'oral_health_treatments.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'desc')
                ->select('oral_health_treatments.*')
                ->get();
        } elseif ($sortBy === 'grade_date') {
            $treatments = $query->join('students', 'oral_health_treatments.student_id', '=', 'students.id')
                ->orderByRaw("
                    CASE 
                        WHEN students.grade_level = 'Kinder 2' OR students.grade_level LIKE 'Kinder%' THEN 1
                        WHEN students.grade_level = 'Grade 1' OR students.grade_level = '1' THEN 2
                        WHEN students.grade_level = 'Grade 2' OR students.grade_level = '2' THEN 3
                        WHEN students.grade_level = 'Grade 3' OR students.grade_level = '3' THEN 4
                        WHEN students.grade_level = 'Grade 4' OR students.grade_level = '4' THEN 5
                        WHEN students.grade_level = 'Grade 5' OR students.grade_level = '5' THEN 6
                        WHEN students.grade_level = 'Grade 6' OR students.grade_level = '6' THEN 7
                        ELSE 99
                    END
                ")
                ->orderBy('oral_health_treatments.date', 'desc')
                ->select('oral_health_treatments.*')
                ->get();
        } else {
            // Default: name_asc
            $treatments = $query->join('students', 'oral_health_treatments.student_id', '=', 'students.id')
                ->orderBy('students.full_name', 'asc')
                ->select('oral_health_treatments.*')
                ->get();
        }

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

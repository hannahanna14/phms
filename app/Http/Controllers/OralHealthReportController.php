<?php

namespace App\Http\Controllers;

use App\Models\OralHealthExamination;
use App\Models\Student;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class OralHealthReportController extends Controller
{
    public function index()
    {
        $gradeLevels = ['All', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Non-Graded'];

        return Inertia::render('OralHealthReport/Index', [
            'gradeLevels' => $gradeLevels,
            'userRole' => Auth::user()->role
        ]);
    }

    /**
     * Get students based on request filters.
     */
    private function getStudents(Request $request)
    {
        if ($request->selected_students && count($request->selected_students) > 0) {
            $studentIds = collect($request->selected_students)
                ->map(fn($student) => is_array($student) ? $student['id'] : $student)
                ->toArray();
            $studentsQuery = Student::whereIn('id', $studentIds);
        } else {
            // Calculate current school year
            $currentYear = date('Y');
            $currentMonth = date('n');
            if ($currentMonth >= 6) {
                $currentSchoolYear = $currentYear . '-' . ($currentYear + 1);
            } else {
                $currentSchoolYear = ($currentYear - 1) . '-' . $currentYear;
            }

            $studentsQuery = Student::where('is_active', true)
                ->where('school_year', $currentSchoolYear);
            $gradeLevel = $request->grade_level;
            if ($gradeLevel && $gradeLevel !== 'All') {
                $studentsQuery->where('grade_level', $gradeLevel);
            }
            if ($request->section && $request->section !== 'All') {
                $studentsQuery->where('section', $request->section);
            }
            if ($request->gender_filter && $request->gender_filter !== 'All') {
                $studentsQuery->where('sex', $request->gender_filter);
            }
            if ($request->min_age) {
                $studentsQuery->where('age', '>=', $request->min_age);
            }
            if ($request->max_age) {
                $studentsQuery->where('age', '<=', $request->max_age);
            }
        }

        $students = $studentsQuery->get();
        $sortBy = $request->sort_by ?? 'Name (A-Z)';

        switch ($sortBy) {
            case 'Name (A-Z)':
                return $this->sortByLastName($students, false);
            case 'Name (Z-A)':
                return $this->sortByLastName($students, true);
            case 'Age (Youngest First)':
                return $students->sortBy('age')->values();
            case 'Age (Oldest First)':
                return $students->sortByDesc('age')->values();
            case 'Grade Level (Lowest First)':
                return $this->sortByGradeLevel($students, false);
            case 'Grade Level (Highest First)':
                return $this->sortByGradeLevel($students, true);
            default:
                return $this->sortByLastName($students, false);
        }
    }

    public function generate(Request $request)
    {
        $students = $this->getStudents($request);
        $reportData = [];

        foreach ($students as $student) {
            $studentData = [
                'name' => $this->formatNameForReport($student->full_name),
                'lrn' => $student->lrn,
                'grade_level' => $student->grade_level,
                'section' => $student->section,
                'gender' => $student->sex,
                'age' => $student->age,
                'birthdate' => $student->birthdate ? $student->birthdate->format('Y-m-d') : null,
            ];

            $oralHealth = OralHealthExamination::where('student_id', $student->id)->latest()->first();
            $selectedOralFields = $request->oral_exam_fields ?? [];
            if (empty($selectedOralFields)) {
                $selectedOralFields = [
                    'permanent_teeth_filled', 'permanent_teeth_decayed', 'permanent_for_extraction',
                    'permanent_for_filling', 'permanent_index_dft', 'temporary_teeth_filled',
                    'temporary_teeth_decayed', 'temporary_for_extraction', 'temporary_for_filling',
                    'temporary_index_dft'
                ];
            }

            foreach ($selectedOralFields as $field) {
                $studentData[$field] = $oralHealth ? ($oralHealth->$field ?? 0) : 0;
            }

            $reportData[] = $studentData;
        }

        return Inertia::render('OralHealthReport/Results', [
            'reportData' => $reportData,
            'grade_level' => $request->grade_level,
            'section' => $request->section,
            'fields' => $request->fields ?? [],
            'oral_exam_fields' => $request->oral_exam_fields ?? [],
            'gender_filter' => $request->gender_filter,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'sort_by' => $request->sort_by,
            'minValues' => $request->minValues ?? [],
            'maxValues' => $request->maxValues ?? [],
            'selected_students' => $request->selected_students ?? []
        ]);
    }

    public function exportPdf(Request $request)
    {
        $students = $this->getStudents($request);
        $chunkSize = (int) config('app.pdf_chunk_size', 500);
        $files = [];
        $selectedFields = $request->fields ?? ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];
        $oralExamFields = $request->oral_exam_fields ?? [];

        foreach ($students->chunk($chunkSize) as $index => $chunk) {
            $reportData = [];
            foreach ($chunk as $student) {
                $studentData = [
                    'name' => $this->formatNameForReport($student->full_name),
                    'lrn' => $student->lrn,
                    'grade_level' => $student->grade_level,
                    'section' => $student->section,
                    'gender' => $student->sex,
                    'age' => $student->age,
                    'birthdate' => $student->birthdate ? $student->birthdate->format('Y-m-d') : null,
                ];

                $oralHealth = OralHealthExamination::where('student_id', $student->id)->latest()->first();
                $oralHealthData = [];
                foreach ($oralExamFields as $field) {
                    $oralHealthData[$field] = $oralHealth ? ($oralHealth->$field ?? 0) : 0;
                }
                $studentData['oral_health_examination'] = $oralHealthData;
                $reportData[] = $studentData;
            }

            $schoolSettings = SchoolSettings::getInstance();
            $pdf = Pdf::loadView('oral-health-report-pdf', [
                'reportData' => $reportData,
                'grade_level' => $request->grade_level,
                'section' => $request->section,
                'fields' => $selectedFields,
                'oral_exam_fields' => $oralExamFields,
                'selected_students' => $request->selected_students ?? [],
                'user_name' => Auth::user()->full_name ?? 'System',
                'schoolSettings' => $schoolSettings
            ]);

            $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('oral_health_report_') . '_part_' . ($index + 1) . '.pdf';
            file_put_contents($tmpPath, $pdf->output());
            $files[] = $tmpPath;

            unset($pdf, $reportData);
            gc_collect_cycles();
        }

        if (count($files) === 1) {
            $filename = 'oral-health-report-grade-' . ($request->grade_level ?: 'selected') . '.pdf';
            return response()->download($files[0], $filename)->deleteFileAfterSend(true);
        }

        $zipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('oral_health_report_zip_') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($files as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();
        }

        foreach ($files as $f) {
            @unlink($f);
        }

        return response()->download($zipPath, 'oral-health-report-grade-' . ($request->grade_level ?: 'selected') . '.zip')->deleteFileAfterSend(true);
    }

    public function searchStudents(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $user = Auth::user();

            if (strlen($query) < 1) {
                if ($user && $user->role === 'teacher') {
                    $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                    if ($assignedStudentIds->isEmpty()) {
                        return response()->json([]);
                    }
                    $students = Student::whereIn('id', $assignedStudentIds)
                        ->select('id', 'full_name', 'lrn', 'grade_level')
                        ->orderBy('full_name')
                        ->get();
                    $result = $students->map(function($student) {
                        $formattedName = $this->formatNameForReport($student->full_name);
                        return [
                            'id' => $student->id,
                            'name' => $formattedName,
                            'lrn' => $student->lrn,
                            'grade_level' => $student->grade_level,
                            'section' => 'N/A',
                            'display_text' => $formattedName . ' (LRN: ' . $student->lrn . ')'
                        ];
                    });
                    return response()->json($result);
                }
                return response()->json([]);
            }

            $studentsQuery = Student::where(function($q) use ($query) {
                $q->whereRaw('LOWER(lrn) LIKE ?', ['%' . strtolower($query) . '%']);
                if (strlen($query) === 1) {
                    $q->orWhere(function($subQ) use ($query) {
                        $subQ->whereRaw('LOWER(full_name) LIKE ?', [strtolower($query) . '%'])
                             ->orWhereRaw('LOWER(full_name) LIKE ?', ['% ' . strtolower($query) . '%']);
                    });
                } else {
                    $q->orWhereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($query) . '%']);
                }
            });

            if ($user->role === 'teacher') {
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                if ($assignedStudentIds->isEmpty()) {
                    return response()->json([]);
                }
                $studentsQuery->whereIn('id', $assignedStudentIds);
            }

            $students = $studentsQuery->select('id', 'full_name', 'lrn', 'grade_level')
                ->orderBy('full_name')
                ->limit(20)
                ->get();

            $result = $students->map(function($student) {
                $formattedName = $this->formatNameForReport($student->full_name);
                return [
                    'id' => $student->id,
                    'name' => $formattedName,
                    'lrn' => $student->lrn,
                    'grade_level' => $student->grade_level,
                    'section' => 'N/A',
                    'display_text' => $formattedName . ' (LRN: ' . $student->lrn . ')'
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Student search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    private function formatNameForReport($fullName)
    {
        if (empty($fullName)) return 'N/A';
        $nameParts = explode(' ', trim($fullName));
        $count = count($nameParts);
        if ($count === 1) return $nameParts[0];
        if ($count === 2) return $nameParts[1] . ', ' . $nameParts[0];
        $lastName = array_pop($nameParts);
        $firstName = array_shift($nameParts);
        $middleInitial = !empty($nameParts) ? strtoupper(substr($nameParts[0], 0, 1)) . '.' : '';
        return $lastName . ', ' . $firstName . ($middleInitial ? ' ' . $middleInitial : '');
    }

    private function getLastName($fullName)
    {
        if (empty($fullName)) return '';
        $nameParts = explode(' ', trim($fullName));
        return end($nameParts);
    }

    private function sortByLastName($students, $descending = false)
    {
        return $students->sort(function($a, $b) use ($descending) {
            $lastNameA = strtolower($this->getLastName($a->full_name));
            $lastNameB = strtolower($this->getLastName($b->full_name));
            $comparison = strcmp($lastNameA, $lastNameB);
            return $descending ? -$comparison : $comparison;
        })->values();
    }

    private function sortByGradeLevel($students, $descending = false)
    {
        $gradeOrder = [
            'Kinder 2' => 0, 'K-2' => 0, 'Grade 1' => 1, '1' => 1,
            'Grade 2' => 2, '2' => 2, 'Grade 3' => 3, '3' => 3,
            'Grade 4' => 4, '4' => 4, 'Grade 5' => 5, '5' => 5,
            'Grade 6' => 6, '6' => 6, 'Grade 7' => 7, '7' => 7,
            'Grade 8' => 8, '8' => 8, 'Grade 9' => 9, '9' => 9,
            'Grade 10' => 10, '10' => 10, 'Grade 11' => 11, '11' => 11,
            'Grade 12' => 12, '12' => 12,
        ];
        return $students->sort(function($a, $b) use ($gradeOrder, $descending) {
            $gradeA = $gradeOrder[$a->grade_level] ?? 999;
            $gradeB = $gradeOrder[$b->grade_level] ?? 999;
            $comparison = $gradeA - $gradeB;
            return $descending ? -$comparison : $comparison;
        })->values();
    }
}

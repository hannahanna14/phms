<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\StudentTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StudentManagementController extends Controller
{
    public function __construct()
    {
        // Only admins can access student management - check in each method instead
    }

    /**
     * Display student management dashboard
     */
    public function index()
    {
        // Only admins can access student management
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can manage students.');
        }

        $students = Student::with(['teacherAssignments' => function($query) {
            $query->latest('school_year');
        }])->orderBy('grade_level')->orderBy('section')->orderBy('full_name')->get();

        $teachers = User::where('role', 'teacher')->orderBy('full_name')->get();
        
        $gradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $sections = ['A', 'B', 'C', 'D', 'E'];
        $currentSchoolYear = $this->getCurrentSchoolYear();

        return Inertia::render('StudentManagement/Index', [
            'students' => $students,
            'teachers' => $teachers,
            'gradeLevels' => $gradeLevels,
            'sections' => $sections,
            'currentSchoolYear' => $currentSchoolYear
        ]);
    }

    /**
     * Promote students to next grade level
     */
    public function promoteStudents(Request $request)
    {
        // Only admins can promote students
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can promote students.');
        }

        $validated = $request->validate([
            'promotions' => 'required|array',
            'promotions.*.student_id' => 'required|exists:students,id',
            'promotions.*.new_grade' => 'required|string',
            'promotions.*.new_section' => 'required|string',
            'promotions.*.new_teacher_id' => 'required|exists:users,id',
            'new_school_year' => 'required|string'
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($validated['promotions'] as $promotion) {
                // Update student's current info
                $student = Student::find($promotion['student_id']);
                $student->update([
                    'grade_level' => $promotion['new_grade'],
                    'section' => $promotion['new_section'],
                    'school_year' => $validated['new_school_year']
                ]);

                // Create new teacher assignment
                StudentTeacherAssignment::create([
                    'student_id' => $promotion['student_id'],
                    'teacher_id' => $promotion['new_teacher_id'],
                    'grade_level' => $promotion['new_grade'],
                    'section' => $promotion['new_section'],
                    'school_year' => $validated['new_school_year']
                ]);

                Log::info('Student promoted', [
                    'student_id' => $promotion['student_id'],
                    'student_name' => $student->full_name,
                    'new_grade' => $promotion['new_grade'],
                    'new_section' => $promotion['new_section'],
                    'new_teacher_id' => $promotion['new_teacher_id'],
                    'school_year' => $validated['new_school_year']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($validated['promotions']) . ' students promoted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student promotion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Promotion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update individual student information
     */
    public function updateStudent(Request $request, Student $student)
    {
        // Only admins can update students
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can update students.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:3|max:20',
            'sex' => 'required|in:Male,Female',
            'lrn' => 'nullable|string|unique:students,lrn,' . $student->id,
            'grade_level' => 'required|string',
            'section' => 'required|string',
            'school_year' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'birthplace' => 'nullable|string',
            'parent_guardian' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully!',
            'student' => $student->fresh()
        ]);
    }

    /**
     * Get students by grade level for promotion planning
     */
    public function getStudentsByGrade(Request $request)
    {
        // Only admins can access this
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can access this.');
        }

        $gradeLevel = $request->get('grade_level');
        
        $students = Student::where('grade_level', $gradeLevel)
            ->with(['teacherAssignments' => function($query) {
                $query->latest('school_year')->first();
            }])
            ->orderBy('section')
            ->orderBy('full_name')
            ->get();

        return response()->json($students);
    }

    /**
     * Bulk assign teacher to students
     */
    public function bulkAssignTeacher(Request $request)
    {
        // Only admins can assign teachers
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Only administrators can assign teachers.');
        }

        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'teacher_id' => 'required|exists:users,id',
            'grade_level' => 'required|string',
            'section' => 'required|string',
            'school_year' => 'required|string'
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($validated['student_ids'] as $studentId) {
                // Update or create teacher assignment
                StudentTeacherAssignment::updateOrCreate([
                    'student_id' => $studentId,
                    'school_year' => $validated['school_year']
                ], [
                    'teacher_id' => $validated['teacher_id'],
                    'grade_level' => $validated['grade_level'],
                    'section' => $validated['section']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($validated['student_ids']) . ' students assigned to teacher successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current school year
     */
    private function getCurrentSchoolYear()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // School year starts in June (month 6)
        if ($currentMonth >= 6) {
            return $currentYear . '-' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '-' . $currentYear;
        }
    }

    /**
     * Generate next grade level
     */
    public function getNextGradeLevel($currentGrade)
    {
        $gradeProgression = [
            'Kinder 2' => 'Grade 1',
            'Grade 1' => 'Grade 2',
            'Grade 2' => 'Grade 3',
            'Grade 3' => 'Grade 4',
            'Grade 4' => 'Grade 5',
            'Grade 5' => 'Grade 6',
            'Grade 6' => 'Graduate'
        ];

        return $gradeProgression[$currentGrade] ?? 'Unknown';
    }
}

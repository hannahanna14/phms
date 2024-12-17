<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidentController extends Controller
{
    public function index()
    {
        $students = Student::select('id', 'full_name', 'age', 'sex')
            ->orderBy('full_name')
            ->get();
            
        return Inertia::render('Incident/Index', [
            'students' => $students
        ]);
    }

    public function show(Student $student)
    {
        $incidents = $student->incidents()
            ->orderBy('incident_date', 'desc')
            ->get();
            
        return Inertia::render('Incident/Show', [
            'student' => $student,
            'incidents' => $incidents
        ]);
    }
}

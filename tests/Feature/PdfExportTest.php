<?php

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns 400 when requesting PDF for too many students', function () {
    $user = new User();
    $user->username = 'adminpdf';
    $user->full_name = 'Admin PDF';
    $user->email = 'adminpdf@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $students = [];
    // Create 900 students to exceed default max of 800
    for ($i = 0; $i < 900; $i++) {
        $s = Student::create([
            'full_name' => "Student {$i}",
            'age' => 6,
            'sex' => 'Male',
            'grade_level' => 'Grade 1',
            'school_year' => '2025-2026'
        ]);
        $students[] = $s->id;
    }

    $response = $this->actingAs($user)->post('/health-report/export-pdf', [
        'selected_students' => $students
    ]);

    $response->assertStatus(400);
    $this->assertStringContainsString('Too many students to generate PDF', $response->json('error'));
});

it('generates pdf for a small set of students', function () {
    $user = new User();
    $user->username = 'adminpdf2';
    $user->full_name = 'Admin PDF2';
    $user->email = 'adminpdf2@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $s = Student::create([
        'full_name' => 'Single Student',
        'age' => 7,
        'sex' => 'Female',
        'grade_level' => 'Grade 1',
        'school_year' => '2025-2026'
    ]);

    $response = $this->actingAs($user)->post('/health-report/export-pdf', [
        'selected_students' => [$s->id]
    ]);

    $response->assertStatus(200);
    $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
});

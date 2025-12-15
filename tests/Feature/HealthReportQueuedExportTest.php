<?php

use App\Jobs\GenerateHealthReportPdf;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('dispatches a queued export job for large exports', function () {
    Bus::fake();

    $user = new User();
    $user->username = 'adminq';
    $user->full_name = 'Admin Q';
    $user->email = 'adminq@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    // create some students
    for ($i = 0; $i < 50; $i++) {
        Student::create([
            'full_name' => "Student {$i}",
            'age' => 6,
            'sex' => 'Male',
            'grade_level' => 'Grade 1',
            'school_year' => '2025-2026'
        ]);
    }

    $response = $this->actingAs($user)->postJson('/health-report/export-pdf/queued', [
        'grade_level' => 'Grade 1',
        'school_year' => '2025-2026',
        'fields' => ['name', 'lrn']
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['status', 'id']);

    Bus::assertDispatched(GenerateHealthReportPdf::class);
});

it('executes the queued job and produces a zip file', function () {
    $user = new User();
    $user->username = 'adminq2';
    $user->full_name = 'Admin Q2';
    $user->email = 'adminq2@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    // create some students
    for ($i = 0; $i < 10; $i++) {
        Student::create([
            'full_name' => "Student {$i}",
            'age' => 6,
            'sex' => 'Male',
            'grade_level' => 'Grade 1',
            'school_year' => '2025-2026'
        ]);
    }

    $exportId = \Illuminate\Support\Str::uuid()->toString();
    $job = new GenerateHealthReportPdf([
        'grade_level' => 'Grade 1',
        'school_year' => '2025-2026',
        'fields' => ['name','lrn']
    ], $user->id, $exportId);

    // Run job synchronously
    $job->handle();

    $zipPath = storage_path('app/exports/' . $exportId . '.zip');
    $this->assertFileExists($zipPath);

    // cleanup
    @unlink($zipPath);
});

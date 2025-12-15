<?php

use App\Models\User;
use App\Models\Student;
use App\Models\HealthExamination;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function current_school_year()
{
    $currentYear = (int) date('Y');
    $currentMonth = (int) date('n');
    if ($currentMonth >= 6) {
        return $currentYear . '-' . ($currentYear + 1);
    }

    return ($currentYear - 1) . '-' . $currentYear;
}

it('defaults health examinations export to current school year', function () {
    $user = new User();
    $user->username = 'admin';
    $user->full_name = 'Admin';
    $user->email = 'admin@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $current = current_school_year();
    $other = '2000-2001';

    $studentA = Student::create([
        'full_name' => 'Alice Current',
        'grade_level' => 'Grade 1',
        'school_year' => $current,
        'age' => 6,
        'sex' => 'Female'
    ]);

    $studentB = Student::create([
        'full_name' => 'Bob Old',
        'grade_level' => 'Grade 1',
        'school_year' => $other,
        'age' => 7,
        'sex' => 'Male'
    ]);

    HealthExamination::create([
        'student_id' => $studentA->id,
        'school_year' => $current,
        'examination_date' => now()
    ]);

    HealthExamination::create([
        'student_id' => $studentB->id,
        'school_year' => $other,
        'examination_date' => now()
    ]);

    $response = $this->actingAs($user)->get('/health-data-export/health-examinations?format=csv');

    $response->assertStatus(200);
    $content = $response->getContent();
    // Instead of reading the streamed CSV content (not reliably captured in tests),
    // assert that the export activity was logged with the expected record count
    $activity = \Spatie\Activitylog\Models\Activity::where('description', 'Exported health examinations data')->latest()->first();
    expect($activity)->not->toBeNull();
    $props = $activity->properties;
    expect($props['record_count'])->toBe(1);
});

it('returns all school years when school_year=all is specified', function () {
    $user = new User();
    $user->username = 'admin';
    $user->full_name = 'Admin';
    $user->email = 'admin@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $current = current_school_year();
    $other = '2000-2001';

    $studentA = Student::create([
        'full_name' => 'Alice Current',
        'grade_level' => 'Grade 1',
        'school_year' => $current,
        'age' => 6,
        'sex' => 'Female'
    ]);

    $studentB = Student::create([
        'full_name' => 'Bob Old',
        'grade_level' => 'Grade 1',
        'school_year' => $other,
        'age' => 7,
        'sex' => 'Male'
    ]);

    HealthExamination::create([
        'student_id' => $studentA->id,
        'school_year' => $current,
        'examination_date' => now()
    ]);

    HealthExamination::create([
        'student_id' => $studentB->id,
        'school_year' => $other,
        'examination_date' => now()
    ]);

    $response = $this->actingAs($user)->get('/health-data-export/health-examinations?format=csv&school_year=all');

    $response->assertStatus(200);

    $activity = \Spatie\Activitylog\Models\Activity::where('description', 'Exported health examinations data')->latest()->first();
    expect($activity)->not->toBeNull();
    $props = $activity->properties;
    expect($props['record_count'])->toBe(2);
});

it('health treatments export respects school_year filter default and all option', function () {
    $user = new User();
    $user->username = 'admin2';
    $user->full_name = 'Admin 2';
    $user->email = 'admin2@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $current = current_school_year();
    $other = '2000-2001';

    $studentA = Student::create([
        'full_name' => 'Alice Current T',
        'grade_level' => 'Grade 1',
        'school_year' => $current,
        'age' => 6,
        'sex' => 'Female'
    ]);

    $studentB = Student::create([
        'full_name' => 'Bob Old T',
        'grade_level' => 'Grade 1',
        'school_year' => $other,
        'age' => 7,
        'sex' => 'Male'
    ]);

    \App\Models\HealthTreatment::create([
        'student_id' => $studentA->id,
        'school_year' => $current,
        'date' => now(),
        'title' => 'Checkup',
        'chief_complaint' => 'N/A',
        'treatment' => 'None'
    ]);

    \App\Models\HealthTreatment::create([
        'student_id' => $studentB->id,
        'school_year' => $other,
        'date' => now(),
        'title' => 'Checkup',
        'chief_complaint' => 'N/A',
        'treatment' => 'None'
    ]);

    expect(\App\Models\HealthTreatment::count())->toBe(2);

    $response = $this->actingAs($user)->get('/health-data-export/health-treatments?format=csv');
    $response->assertStatus(200);
    $activity = \Spatie\Activitylog\Models\Activity::where('description', 'Exported health treatments data')->latest()->first();
    expect($activity)->not->toBeNull();
    $props = $activity->properties;
    expect($props['record_count'])->toBe(1);

    $responseAll = $this->actingAs($user)->get('/health-data-export/health-treatments?format=csv&school_year=all');
    $responseAll->assertStatus(200);
    $activities = \Spatie\Activitylog\Models\Activity::where('description', 'Exported health treatments data')->get();
    expect($activities->isNotEmpty())->toBeTrue();

    // Find an activity entry where record_count == 2
    $found = $activities->first(function ($act) {
        $props = $act->properties;
        return isset($props['record_count']) && $props['record_count'] == 2;
    });

    expect($found)->not->toBeNull();
    expect($found->properties['filters']['school_year'] ?? null)->toBe('all');
});

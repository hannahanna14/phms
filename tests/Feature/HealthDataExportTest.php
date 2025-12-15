<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows admin to export health examinations as csv', function () {
    // Create an admin user
    $user = new User();
    $user->username = 'admintest';
    $user->full_name = 'Admin Test';
    $user->email = 'admin@example.test';
    $user->password = bcrypt('password');
    $user->role = 'admin';
    $user->save();

    $response = $this->actingAs($user)
        ->get('/health-data-export/health-examinations?format=csv');

    $response->assertStatus(200);
    $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
});

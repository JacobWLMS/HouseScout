<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('pulse dashboard is accessible when authorized', function () {
    Gate::define('viewPulse', fn ($user) => true);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/pulse');

    $response->assertStatus(200);
});

test('pulse dashboard is forbidden when not authorized', function () {
    Gate::define('viewPulse', fn ($user) => false);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/pulse');

    $response->assertForbidden();
});

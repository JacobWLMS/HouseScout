<?php

use App\Models\User;

test('dashboard loads for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/app');

    $response->assertStatus(200);
});

test('dashboard redirects unauthenticated users to login', function () {
    $response = $this->get('/app');

    $response->assertRedirect('/app/login');
});

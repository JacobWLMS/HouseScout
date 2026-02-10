<?php

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function () {
    $response = $this->getJson('/api/user');

    $response->assertStatus(401);
});

test('authenticated user can get their profile', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->getJson('/api/user');

    $response->assertOk();
});

test('authenticated user can get property details', function () {
    Sanctum::actingAs(User::factory()->create());
    $property = Property::factory()->create();

    $response = $this->getJson("/api/properties/{$property->id}");

    $response->assertOk();
});

test('authenticated user can get saved properties', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $property = Property::factory()->create();
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $property->id]);

    $response = $this->getJson('/api/saved-properties');

    $response->assertOk()
        ->assertJsonCount(1);
});

<?php

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;

test('property detail page loads with a valid property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $response = $this->actingAs($user)->get("/app/properties/{$property->id}");

    $response->assertStatus(200);
    $response->assertSee($property->postcode);
});

test('property detail page requires authentication', function () {
    $property = Property::factory()->create();

    $response = $this->get("/app/properties/{$property->id}");

    $response->assertRedirect('/app/login');
});

test('save toggle creates a saved property record', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->actingAs($user);

    Livewire\Livewire::test(\App\Filament\Pages\PropertyDetailPage::class, ['property' => $property])
        ->callAction('toggleSave');

    $this->assertDatabaseHas('saved_properties', [
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);
});

test('save toggle removes saved property when already saved', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->actingAs($user);

    Livewire\Livewire::test(\App\Filament\Pages\PropertyDetailPage::class, ['property' => $property])
        ->callAction('toggleSave');

    $this->assertDatabaseMissing('saved_properties', [
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);
});

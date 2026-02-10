<?php

use App\Filament\Resources\SavedProperties\Pages\ManageSavedProperties;
use App\Models\EpcData;
use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('saved properties page loads for authenticated user', function () {
    $response = $this->get('/app/saved-properties');

    $response->assertStatus(200);
});

test('saved properties page requires authentication', function () {
    auth()->logout();

    $response = $this->get('/app/saved-properties');

    $response->assertRedirect('/app/login');
});

test('page shows only current users saved properties', function () {
    $otherUser = User::factory()->create();

    $myProperty = Property::factory()->create(['address_line_1' => '10 Downing Street']);
    $otherProperty = Property::factory()->create(['address_line_1' => '11 Downing Street']);

    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $myProperty->id,
    ]);

    SavedProperty::factory()->create([
        'user_id' => $otherUser->id,
        'property_id' => $otherProperty->id,
    ]);

    Livewire::test(ManageSavedProperties::class)
        ->assertSee('10 Downing Street')
        ->assertDontSee('11 Downing Street');
});

test('table columns render correctly', function () {
    $property = Property::factory()->create([
        'address_line_1' => '42 Baker Street',
        'postcode' => 'NW1 6XE',
    ]);

    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'C',
    ]);

    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'notes' => 'Great location',
    ]);

    Livewire::test(ManageSavedProperties::class)
        ->assertSee('42 Baker Street')
        ->assertSee('NW1 6XE')
        ->assertSee('Great location');
});

test('can delete a saved property', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    Livewire::test(ManageSavedProperties::class)
        ->callTableAction('delete', $saved);

    $this->assertDatabaseMissing('saved_properties', [
        'id' => $saved->id,
    ]);
});

test('empty state works when no saved properties', function () {
    Livewire::test(ManageSavedProperties::class)
        ->assertSuccessful();
});

test('default sort is by created_at descending', function () {
    $property1 = Property::factory()->create(['address_line_1' => 'First Saved']);
    $property2 = Property::factory()->create(['address_line_1' => 'Second Saved']);

    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property1->id,
        'created_at' => now()->subDay(),
    ]);

    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property2->id,
        'created_at' => now(),
    ]);

    Livewire::test(ManageSavedProperties::class)
        ->assertSeeInOrder(['Second Saved', 'First Saved']);
});

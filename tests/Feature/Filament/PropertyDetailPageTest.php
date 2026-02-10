<?php

use App\Filament\Pages\PropertyDetailPage;
use App\Models\CrimeData;
use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\LandRegistryData;
use App\Models\PlanningApplication;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('page heading shows address and postcode', function () {
    $property = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('10 Downing Street, SW1A 2AA');
});

test('page subheading shows city and county', function () {
    $property = Property::factory()->create([
        'address_line_2' => 'Westminster',
        'city' => 'London',
        'county' => 'Greater London',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Westminster, London, Greater London');
});

test('page subheading shows city only when county is null', function () {
    $property = Property::factory()->create([
        'address_line_2' => null,
        'city' => 'London',
        'county' => null,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('London');
});

test('breadcrumbs include dashboard and postcode', function () {
    $property = Property::factory()->create(['postcode' => 'SW1A 2AA']);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    $breadcrumbs = $component->instance()->getBreadcrumbs();

    expect($breadcrumbs)->toBe([
        '/' => 'Dashboard',
        '#' => 'SW1A 2AA',
    ]);
});

test('saveNotes without saved property shows warning notification', function () {
    $property = Property::factory()->create();

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->set('notes', 'Some notes')
        ->call('saveNotes')
        ->assertNotified('Save the property first to add notes');
});

test('saveNotes with saved property updates notes', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'notes' => 'Old notes',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->set('notes', 'Updated notes')
        ->call('saveNotes')
        ->assertNotified('Notes saved');

    expect($saved->fresh()->notes)->toBe('Updated notes');
});

test('demand count is displayed correctly', function () {
    $property = Property::factory()->create();

    // Create 3 searches from different users within 30 days
    $users = User::factory()->count(3)->create();
    foreach ($users as $user) {
        PropertySearch::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'searched_at' => now()->subDays(5),
        ]);
    }

    // Create 1 search older than 30 days (shouldn't count)
    PropertySearch::factory()->create([
        'user_id' => User::factory(),
        'property_id' => $property->id,
        'searched_at' => now()->subDays(45),
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->demandCount)->toBe(3);
});

test('page loads with all relationship data', function () {
    $property = Property::factory()->create();
    EpcData::factory()->create(['property_id' => $property->id]);
    FloodRiskData::factory()->create(['property_id' => $property->id]);
    LandRegistryData::factory()->create(['property_id' => $property->id]);
    PlanningApplication::factory()->create(['property_id' => $property->id]);
    CrimeData::factory()->create(['property_id' => $property->id]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    $loadedProperty = $component->instance()->property;

    expect($loadedProperty->relationLoaded('epcData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('floodRiskData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('landRegistryData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('planningApplications'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('crimeData'))->toBeTrue()
        ->and($loadedProperty->epcData)->not->toBeNull()
        ->and($loadedProperty->floodRiskData)->not->toBeNull()
        ->and($loadedProperty->landRegistryData)->not->toBeNull()
        ->and($loadedProperty->planningApplications)->toHaveCount(1)
        ->and($loadedProperty->crimeData)->toHaveCount(1);
});

test('non-existent property returns 404', function () {
    $response = $this->get('/app/properties/99999');

    $response->assertStatus(404);
});

test('saved property notes are loaded on mount', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'notes' => 'My property notes',
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->notes)->toBe('My property notes')
        ->and($component->instance()->savedProperty)->not->toBeNull();
});

test('unsaved property has empty notes on mount', function () {
    $property = Property::factory()->create();

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->notes)->toBe('')
        ->and($component->instance()->savedProperty)->toBeNull();
});

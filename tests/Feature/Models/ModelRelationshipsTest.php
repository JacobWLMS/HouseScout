<?php

use App\Models\CrimeData;
use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\LandRegistryData;
use App\Models\PlanningApplication;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\SavedProperty;
use App\Models\User;

// --- Property hasOne inverse (belongsTo) ---

test('epc data belongs to property', function () {
    $property = Property::factory()->create();
    $epc = EpcData::factory()->create(['property_id' => $property->id]);

    expect($epc->property)->toBeInstanceOf(Property::class)
        ->and($epc->property->id)->toBe($property->id);
});

test('flood risk data belongs to property', function () {
    $property = Property::factory()->create();
    $flood = FloodRiskData::factory()->create(['property_id' => $property->id]);

    expect($flood->property)->toBeInstanceOf(Property::class)
        ->and($flood->property->id)->toBe($property->id);
});

test('land registry data belongs to property', function () {
    $property = Property::factory()->create();
    $registry = LandRegistryData::factory()->create(['property_id' => $property->id]);

    expect($registry->property)->toBeInstanceOf(Property::class)
        ->and($registry->property->id)->toBe($property->id);
});

// --- Property hasMany inverse (belongsTo) ---

test('planning application belongs to property', function () {
    $property = Property::factory()->create();
    $application = PlanningApplication::factory()->create(['property_id' => $property->id]);

    expect($application->property)->toBeInstanceOf(Property::class)
        ->and($application->property->id)->toBe($property->id);
});

test('crime data belongs to property', function () {
    $property = Property::factory()->create();
    $crime = CrimeData::factory()->create(['property_id' => $property->id]);

    expect($crime->property)->toBeInstanceOf(Property::class)
        ->and($crime->property->id)->toBe($property->id);
});

test('saved property belongs to property', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);

    expect($saved->property)->toBeInstanceOf(Property::class)
        ->and($saved->property->id)->toBe($property->id);
});

test('saved property belongs to user', function () {
    $user = User::factory()->create();
    $saved = SavedProperty::factory()->create(['user_id' => $user->id]);

    expect($saved->user)->toBeInstanceOf(User::class)
        ->and($saved->user->id)->toBe($user->id);
});

test('property search belongs to user', function () {
    $user = User::factory()->create();
    $search = PropertySearch::factory()->create(['user_id' => $user->id]);

    expect($search->user)->toBeInstanceOf(User::class)
        ->and($search->user->id)->toBe($user->id);
});

test('property search belongs to property', function () {
    $property = Property::factory()->create();
    $search = PropertySearch::factory()->create(['property_id' => $property->id]);

    expect($search->property)->toBeInstanceOf(Property::class)
        ->and($search->property->id)->toBe($property->id);
});

// --- Property hasMany counts ---

test('property has many property searches', function () {
    $property = Property::factory()->create();
    PropertySearch::factory()->count(4)->create(['property_id' => $property->id]);

    expect($property->propertySearches)->toHaveCount(4)
        ->each->toBeInstanceOf(PropertySearch::class);
});

test('property has many crime data records', function () {
    $property = Property::factory()->create();
    $categories = ['burglary', 'robbery', 'vehicle-crime'];
    foreach ($categories as $category) {
        CrimeData::factory()->create([
            'property_id' => $property->id,
            'month' => '2025-01',
            'category' => $category,
        ]);
    }

    expect($property->crimeData)->toHaveCount(3);
});

test('property has many planning applications', function () {
    $property = Property::factory()->create();
    PlanningApplication::factory()->count(2)->create(['property_id' => $property->id]);

    expect($property->planningApplications)->toHaveCount(2)
        ->each->toBeInstanceOf(PlanningApplication::class);
});

// --- User hasMany ---

test('user has many property searches', function () {
    $user = User::factory()->create();
    PropertySearch::factory()->count(5)->create(['user_id' => $user->id]);

    expect($user->propertySearches)->toHaveCount(5)
        ->each->toBeInstanceOf(PropertySearch::class);
});

test('user has many saved properties', function () {
    $user = User::factory()->create();
    SavedProperty::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->savedProperties)->toHaveCount(3)
        ->each->toBeInstanceOf(SavedProperty::class);
});

// --- Relationship returns null/empty when no related records ---

test('property epc data returns null when none exists', function () {
    $property = Property::factory()->create();

    expect($property->epcData)->toBeNull();
});

test('property flood risk data returns null when none exists', function () {
    $property = Property::factory()->create();

    expect($property->floodRiskData)->toBeNull();
});

test('property land registry data returns null when none exists', function () {
    $property = Property::factory()->create();

    expect($property->landRegistryData)->toBeNull();
});

test('property returns empty collection when no planning applications', function () {
    $property = Property::factory()->create();

    expect($property->planningApplications)->toBeEmpty();
});

test('property returns empty collection when no crime data', function () {
    $property = Property::factory()->create();

    expect($property->crimeData)->toBeEmpty();
});

test('property returns empty collection when no saved properties', function () {
    $property = Property::factory()->create();

    expect($property->savedProperties)->toBeEmpty();
});

test('user returns empty collection when no property searches', function () {
    $user = User::factory()->create();

    expect($user->propertySearches)->toBeEmpty();
});

test('user returns empty collection when no saved properties', function () {
    $user = User::factory()->create();

    expect($user->savedProperties)->toBeEmpty();
});

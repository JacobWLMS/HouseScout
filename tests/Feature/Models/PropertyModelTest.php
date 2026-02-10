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

test('property can be created with factory', function () {
    $property = Property::factory()->create();

    expect($property)->toBeInstanceOf(Property::class)
        ->and($property->address_line_1)->not->toBeEmpty()
        ->and($property->city)->not->toBeEmpty()
        ->and($property->postcode)->not->toBeEmpty();
});

test('property search can be created with factory', function () {
    $search = PropertySearch::factory()->create();

    expect($search)->toBeInstanceOf(PropertySearch::class)
        ->and($search->user_id)->toBeInt()
        ->and($search->property_id)->toBeInt()
        ->and($search->search_query)->not->toBeEmpty();
});

test('epc data can be created with factory', function () {
    $epc = EpcData::factory()->create();

    expect($epc)->toBeInstanceOf(EpcData::class)
        ->and($epc->current_energy_rating)->toBeIn(['A', 'B', 'C', 'D', 'E', 'F', 'G'])
        ->and($epc->current_energy_efficiency)->toBeBetween(1, 100);
});

test('planning application can be created with factory', function () {
    $application = PlanningApplication::factory()->create();

    expect($application)->toBeInstanceOf(PlanningApplication::class)
        ->and($application->reference)->not->toBeEmpty()
        ->and($application->status)->toBeIn(['Approved', 'Refused', 'Pending', 'Withdrawn', 'Appealed']);
});

test('flood risk data can be created with factory', function () {
    $flood = FloodRiskData::factory()->create();

    expect($flood)->toBeInstanceOf(FloodRiskData::class)
        ->and($flood->flood_risk_level)->toBeIn(['Very Low', 'Low', 'Medium', 'High']);
});

test('crime data can be created with factory', function () {
    $crime = CrimeData::factory()->create();

    expect($crime)->toBeInstanceOf(CrimeData::class)
        ->and($crime->month)->toMatch('/^\d{4}-\d{2}$/')
        ->and($crime->count)->toBeGreaterThanOrEqual(0);
});

test('land registry data can be created with factory', function () {
    $registry = LandRegistryData::factory()->create();

    expect($registry)->toBeInstanceOf(LandRegistryData::class)
        ->and($registry->tenure)->toBeIn(['Freehold', 'Leasehold']);
});

test('saved property can be created with factory', function () {
    $saved = SavedProperty::factory()->create();

    expect($saved)->toBeInstanceOf(SavedProperty::class)
        ->and($saved->user_id)->toBeInt()
        ->and($saved->property_id)->toBeInt();
});

test('property has epc data relationship', function () {
    $property = Property::factory()->create();
    $epc = EpcData::factory()->create(['property_id' => $property->id]);

    expect($property->epcData)->toBeInstanceOf(EpcData::class)
        ->and($property->epcData->id)->toBe($epc->id);
});

test('property has planning applications relationship', function () {
    $property = Property::factory()->create();
    PlanningApplication::factory()->count(3)->create(['property_id' => $property->id]);

    expect($property->planningApplications)->toHaveCount(3);
});

test('property has flood risk data relationship', function () {
    $property = Property::factory()->create();
    $flood = FloodRiskData::factory()->create(['property_id' => $property->id]);

    expect($property->floodRiskData)->toBeInstanceOf(FloodRiskData::class)
        ->and($property->floodRiskData->id)->toBe($flood->id);
});

test('property has crime data relationship', function () {
    $property = Property::factory()->create();
    CrimeData::factory()->count(5)->create(['property_id' => $property->id]);

    expect($property->crimeData)->toHaveCount(5);
});

test('property has land registry data relationship', function () {
    $property = Property::factory()->create();
    $registry = LandRegistryData::factory()->create(['property_id' => $property->id]);

    expect($property->landRegistryData)->toBeInstanceOf(LandRegistryData::class)
        ->and($property->landRegistryData->id)->toBe($registry->id);
});

test('property has saved properties relationship', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->count(2)->create(['property_id' => $property->id]);

    expect($property->savedProperties)->toHaveCount(2);
});

test('user has property searches relationship', function () {
    $user = User::factory()->create();
    PropertySearch::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->propertySearches)->toHaveCount(3);
});

test('user has saved properties relationship', function () {
    $user = User::factory()->create();
    SavedProperty::factory()->count(2)->create(['user_id' => $user->id]);

    expect($user->savedProperties)->toHaveCount(2);
});

test('saved property has unique constraint on user and property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    expect(fn () => SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});

test('crime data has unique constraint on property month and category', function () {
    $property = Property::factory()->create();

    CrimeData::factory()->create([
        'property_id' => $property->id,
        'month' => '2025-01',
        'category' => 'burglary',
    ]);

    expect(fn () => CrimeData::factory()->create([
        'property_id' => $property->id,
        'month' => '2025-01',
        'category' => 'burglary',
    ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});

test('epc data has unique constraint on property', function () {
    $property = Property::factory()->create();

    EpcData::factory()->create(['property_id' => $property->id]);

    expect(fn () => EpcData::factory()->create(['property_id' => $property->id]))
        ->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});

test('property casts metadata to array', function () {
    $property = Property::factory()->create(['metadata' => ['key' => 'value']]);

    $property->refresh();

    expect($property->metadata)->toBeArray()
        ->and($property->metadata['key'])->toBe('value');
});

test('epc data casts raw response to array', function () {
    $epc = EpcData::factory()->create(['raw_response' => ['data' => 'test']]);

    $epc->refresh();

    expect($epc->raw_response)->toBeArray()
        ->and($epc->raw_response['data'])->toBe('test');
});

test('property search belongs to user and property', function () {
    $search = PropertySearch::factory()->create();

    expect($search->user)->toBeInstanceOf(User::class)
        ->and($search->property)->toBeInstanceOf(Property::class);
});

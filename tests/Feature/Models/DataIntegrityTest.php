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
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

// --- Required field constraints ---

test('property requires address_line_1', function () {
    expect(fn () => Property::factory()->create(['address_line_1' => null]))
        ->toThrow(QueryException::class);
});

test('property requires city', function () {
    expect(fn () => Property::factory()->create(['city' => null]))
        ->toThrow(QueryException::class);
});

test('property requires postcode', function () {
    expect(fn () => Property::factory()->create(['postcode' => null]))
        ->toThrow(QueryException::class);
});

test('property search requires user_id', function () {
    expect(fn () => PropertySearch::factory()->create(['user_id' => null]))
        ->toThrow(QueryException::class);
});

test('property search requires property_id', function () {
    expect(fn () => PropertySearch::factory()->create(['property_id' => null]))
        ->toThrow(QueryException::class);
});

test('saved property requires user_id', function () {
    expect(fn () => SavedProperty::factory()->create(['user_id' => null]))
        ->toThrow(QueryException::class);
});

test('saved property requires property_id', function () {
    expect(fn () => SavedProperty::factory()->create(['property_id' => null]))
        ->toThrow(QueryException::class);
});

// --- Unique constraints ---

test('property uprn must be unique when not null', function () {
    Property::factory()->create(['uprn' => '123456789012']);

    expect(fn () => Property::factory()->create(['uprn' => '123456789012']))
        ->toThrow(UniqueConstraintViolationException::class);
});

test('property allows multiple null uprn values', function () {
    Property::factory()->count(3)->create(['uprn' => null]);

    expect(Property::whereNull('uprn')->count())->toBe(3);
});

test('planning application has unique constraint on property_id and reference', function () {
    $property = Property::factory()->create();
    PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'reference' => 'APP/2025/0001',
    ]);

    expect(fn () => PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'reference' => 'APP/2025/0001',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

test('flood risk data has unique constraint on property_id', function () {
    $property = Property::factory()->create();
    FloodRiskData::factory()->create(['property_id' => $property->id]);

    expect(fn () => FloodRiskData::factory()->create(['property_id' => $property->id]))
        ->toThrow(UniqueConstraintViolationException::class);
});

test('land registry data has unique constraint on property_id', function () {
    $property = Property::factory()->create();
    LandRegistryData::factory()->create(['property_id' => $property->id]);

    expect(fn () => LandRegistryData::factory()->create(['property_id' => $property->id]))
        ->toThrow(UniqueConstraintViolationException::class);
});

// --- Cascade deletes ---

test('deleting property cascades to epc data', function () {
    $property = Property::factory()->create();
    EpcData::factory()->create(['property_id' => $property->id]);

    $property->delete();

    expect(EpcData::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to flood risk data', function () {
    $property = Property::factory()->create();
    FloodRiskData::factory()->create(['property_id' => $property->id]);

    $property->delete();

    expect(FloodRiskData::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to land registry data', function () {
    $property = Property::factory()->create();
    LandRegistryData::factory()->create(['property_id' => $property->id]);

    $property->delete();

    expect(LandRegistryData::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to planning applications', function () {
    $property = Property::factory()->create();
    PlanningApplication::factory()->count(3)->create(['property_id' => $property->id]);

    $property->delete();

    expect(PlanningApplication::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to crime data', function () {
    $property = Property::factory()->create();
    $categories = ['burglary', 'robbery', 'vehicle-crime', 'shoplifting', 'drugs'];
    foreach ($categories as $category) {
        CrimeData::factory()->create([
            'property_id' => $property->id,
            'month' => '2025-01',
            'category' => $category,
        ]);
    }

    $property->delete();

    expect(CrimeData::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to property searches', function () {
    $property = Property::factory()->create();
    PropertySearch::factory()->count(2)->create(['property_id' => $property->id]);

    $property->delete();

    expect(PropertySearch::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting property cascades to saved properties', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->count(2)->create(['property_id' => $property->id]);

    $property->delete();

    expect(SavedProperty::where('property_id', $property->id)->count())->toBe(0);
});

test('deleting user cascades to property searches', function () {
    $user = User::factory()->create();
    PropertySearch::factory()->count(3)->create(['user_id' => $user->id]);

    $user->delete();

    expect(PropertySearch::where('user_id', $user->id)->count())->toBe(0);
});

test('deleting user cascades to saved properties', function () {
    $user = User::factory()->create();
    SavedProperty::factory()->count(2)->create(['user_id' => $user->id]);

    $user->delete();

    expect(SavedProperty::where('user_id', $user->id)->count())->toBe(0);
});

// --- Nullable fields ---

test('property can be created with all nullable fields as null', function () {
    $property = Property::factory()->create([
        'address_line_2' => null,
        'county' => null,
        'uprn' => null,
        'latitude' => null,
        'longitude' => null,
        'property_type' => null,
        'built_form' => null,
        'floor_area' => null,
        'metadata' => null,
    ]);

    $property->refresh();

    expect($property->address_line_2)->toBeNull()
        ->and($property->county)->toBeNull()
        ->and($property->uprn)->toBeNull()
        ->and($property->latitude)->toBeNull()
        ->and($property->longitude)->toBeNull()
        ->and($property->property_type)->toBeNull()
        ->and($property->built_form)->toBeNull()
        ->and($property->floor_area)->toBeNull()
        ->and($property->metadata)->toBeNull();
});

test('epc data can be created with all nullable fields as null', function () {
    $epc = EpcData::factory()->create([
        'current_energy_rating' => null,
        'potential_energy_rating' => null,
        'current_energy_efficiency' => null,
        'potential_energy_efficiency' => null,
        'environment_impact_current' => null,
        'environment_impact_potential' => null,
        'energy_consumption_current' => null,
        'energy_consumption_potential' => null,
        'co2_emissions_current' => null,
        'co2_emissions_potential' => null,
        'lighting_cost_current' => null,
        'lighting_cost_potential' => null,
        'heating_cost_current' => null,
        'heating_cost_potential' => null,
        'hot_water_cost_current' => null,
        'hot_water_cost_potential' => null,
        'main_heating_description' => null,
        'main_fuel_type' => null,
        'lodgement_date' => null,
        'raw_response' => null,
        'fetched_at' => null,
    ]);

    $epc->refresh();

    expect($epc->current_energy_rating)->toBeNull()
        ->and($epc->fetched_at)->toBeNull()
        ->and($epc->lodgement_date)->toBeNull()
        ->and($epc->raw_response)->toBeNull();
});

test('saved property notes can be null', function () {
    $saved = SavedProperty::factory()->create(['notes' => null]);

    $saved->refresh();

    expect($saved->notes)->toBeNull();
});

// --- Model casts ---

test('property casts latitude as decimal:7', function () {
    $property = Property::factory()->create(['latitude' => 51.5074123]);

    $property->refresh();

    expect($property->latitude)->toBe('51.5074123');
});

test('property casts longitude as decimal:7', function () {
    $property = Property::factory()->create(['longitude' => -0.1277583]);

    $property->refresh();

    expect($property->longitude)->toBe('-0.1277583');
});

test('property casts floor_area as decimal:2', function () {
    $property = Property::factory()->create(['floor_area' => 125.50]);

    $property->refresh();

    expect($property->floor_area)->toBe('125.50');
});

test('property casts metadata as array', function () {
    $metadata = ['source' => 'epc', 'confidence' => 0.95];
    $property = Property::factory()->create(['metadata' => $metadata]);

    $property->refresh();

    expect($property->metadata)->toBeArray()
        ->and($property->metadata['source'])->toBe('epc')
        ->and($property->metadata['confidence'])->toBe(0.95);
});

test('epc data casts co2_emissions_current as decimal:2', function () {
    $epc = EpcData::factory()->create(['co2_emissions_current' => 5.43]);

    $epc->refresh();

    expect($epc->co2_emissions_current)->toBe('5.43');
});

test('epc data casts lodgement_date as date', function () {
    $epc = EpcData::factory()->create(['lodgement_date' => '2024-06-15']);

    $epc->refresh();

    expect($epc->lodgement_date)->toBeInstanceOf(Carbon\CarbonImmutable::class)
        ->and($epc->lodgement_date->format('Y-m-d'))->toBe('2024-06-15');
});

test('epc data casts fetched_at as datetime', function () {
    $epc = EpcData::factory()->create(['fetched_at' => '2025-01-15 10:30:00']);

    $epc->refresh();

    expect($epc->fetched_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('epc data casts raw_response as array', function () {
    $raw = ['rows' => [['column1' => 'value1']]];
    $epc = EpcData::factory()->create(['raw_response' => $raw]);

    $epc->refresh();

    expect($epc->raw_response)->toBeArray()
        ->and($epc->raw_response['rows'])->toBeArray();
});

test('planning application casts decision_date as date', function () {
    $app = PlanningApplication::factory()->create(['decision_date' => '2025-03-20']);

    $app->refresh();

    expect($app->decision_date)->toBeInstanceOf(Carbon\CarbonImmutable::class)
        ->and($app->decision_date->format('Y-m-d'))->toBe('2025-03-20');
});

test('planning application casts application_date as date', function () {
    $app = PlanningApplication::factory()->create(['application_date' => '2024-11-10']);

    $app->refresh();

    expect($app->application_date)->toBeInstanceOf(Carbon\CarbonImmutable::class)
        ->and($app->application_date->format('Y-m-d'))->toBe('2024-11-10');
});

test('planning application casts raw_response as array', function () {
    $raw = ['data' => 'test'];
    $app = PlanningApplication::factory()->create(['raw_response' => $raw]);

    $app->refresh();

    expect($app->raw_response)->toBeArray()
        ->and($app->raw_response['data'])->toBe('test');
});

test('flood risk data casts active_warnings as array', function () {
    $warnings = ['Flood warning for River Thames', 'Surface water alert'];
    $flood = FloodRiskData::factory()->create(['active_warnings' => $warnings]);

    $flood->refresh();

    expect($flood->active_warnings)->toBeArray()->toHaveCount(2)
        ->and($flood->active_warnings[0])->toBe('Flood warning for River Thames');
});

test('flood risk data casts fetched_at as datetime', function () {
    $flood = FloodRiskData::factory()->create(['fetched_at' => '2025-01-20 14:00:00']);

    $flood->refresh();

    expect($flood->fetched_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('land registry data casts last_sold_date as date', function () {
    $registry = LandRegistryData::factory()->create(['last_sold_date' => '2020-05-15']);

    $registry->refresh();

    expect($registry->last_sold_date)->toBeInstanceOf(Carbon\CarbonImmutable::class)
        ->and($registry->last_sold_date->format('Y-m-d'))->toBe('2020-05-15');
});

test('land registry data casts price_history as array', function () {
    $history = [['date' => '2015-01-01', 'price' => 250000]];
    $registry = LandRegistryData::factory()->create(['price_history' => $history]);

    $registry->refresh();

    expect($registry->price_history)->toBeArray()
        ->and($registry->price_history[0]['price'])->toBe(250000);
});

test('crime data casts count as integer', function () {
    $crime = CrimeData::factory()->create(['count' => 7]);

    $crime->refresh();

    expect($crime->count)->toBeInt()->toBe(7);
});

test('property search casts searched_at as datetime', function () {
    $search = PropertySearch::factory()->create(['searched_at' => '2025-02-01 09:15:00']);

    $search->refresh();

    expect($search->searched_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('user casts email_verified_at as datetime', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('user casts password as hashed', function () {
    $user = User::factory()->create(['password' => 'plaintext']);

    expect($user->password)->not->toBe('plaintext')
        ->and(password_verify('plaintext', $user->password))->toBeTrue();
});

// --- Timestamps ---

test('property has timestamps set on creation', function () {
    $property = Property::factory()->create();

    expect($property->created_at)->not->toBeNull()
        ->and($property->updated_at)->not->toBeNull()
        ->and($property->created_at)->toBeInstanceOf(Carbon\CarbonImmutable::class)
        ->and($property->updated_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('epc data has timestamps set on creation', function () {
    $epc = EpcData::factory()->create();

    expect($epc->created_at)->not->toBeNull()
        ->and($epc->updated_at)->not->toBeNull();
});

test('crime data has timestamps set on creation', function () {
    $crime = CrimeData::factory()->create();

    expect($crime->created_at)->not->toBeNull()
        ->and($crime->updated_at)->not->toBeNull();
});

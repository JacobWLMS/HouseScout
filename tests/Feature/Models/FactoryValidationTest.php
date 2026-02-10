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

// --- Property Factory ---

test('property factory populates all required fields', function () {
    $property = Property::factory()->create();

    expect($property->address_line_1)->toBeString()->not->toBeEmpty()
        ->and($property->city)->toBeString()->not->toBeEmpty()
        ->and($property->postcode)->toBeString()->not->toBeEmpty()
        ->and($property->id)->toBeInt();
});

test('multiple properties can be created without unique constraint violations', function () {
    $properties = Property::factory()->count(10)->create();

    expect($properties)->toHaveCount(10);
    expect(Property::count())->toBe(10);
});

test('property factory generates valid latitude and longitude ranges', function () {
    $property = Property::factory()->create();

    if ($property->latitude !== null) {
        expect((float) $property->latitude)->toBeGreaterThanOrEqual(50.0)
            ->toBeLessThanOrEqual(55.8);
    }

    if ($property->longitude !== null) {
        expect((float) $property->longitude)->toBeGreaterThanOrEqual(-5.7)
            ->toBeLessThanOrEqual(1.8);
    }
});

// --- User Factory ---

test('user factory populates all required fields', function () {
    $user = User::factory()->create();

    expect($user->name)->toBeString()->not->toBeEmpty()
        ->and($user->email)->toBeString()->toContain('@')
        ->and($user->password)->toBeString()->not->toBeEmpty()
        ->and($user->email_verified_at)->not->toBeNull();
});

test('multiple users can be created with unique emails', function () {
    $users = User::factory()->count(10)->create();

    expect($users)->toHaveCount(10);

    $emails = $users->pluck('email')->toArray();
    expect(array_unique($emails))->toHaveCount(10);
});

test('user factory unverified state sets email_verified_at to null', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

// --- PropertySearch Factory ---

test('property search factory populates all required fields', function () {
    $search = PropertySearch::factory()->create();

    expect($search->user_id)->toBeInt()
        ->and($search->property_id)->toBeInt()
        ->and($search->searched_at)->not->toBeNull()
        ->and($search->search_query)->toBeString()->not->toBeEmpty();
});

test('property search factory creates related user and property', function () {
    $search = PropertySearch::factory()->create();

    expect($search->user)->toBeInstanceOf(User::class)
        ->and($search->property)->toBeInstanceOf(Property::class);
});

test('multiple property searches can be created', function () {
    $searches = PropertySearch::factory()->count(5)->create();

    expect($searches)->toHaveCount(5);
});

// --- SavedProperty Factory ---

test('saved property factory populates all required fields', function () {
    $saved = SavedProperty::factory()->create();

    expect($saved->user_id)->toBeInt()
        ->and($saved->property_id)->toBeInt();
});

test('saved property factory creates related user and property', function () {
    $saved = SavedProperty::factory()->create();

    expect($saved->user)->toBeInstanceOf(User::class)
        ->and($saved->property)->toBeInstanceOf(Property::class);
});

// --- EpcData Factory ---

test('epc data factory populates all required fields', function () {
    $epc = EpcData::factory()->create();

    expect($epc->property_id)->toBeInt()
        ->and($epc->current_energy_rating)->toBeIn(['A', 'B', 'C', 'D', 'E', 'F', 'G'])
        ->and($epc->potential_energy_rating)->toBeIn(['A', 'B', 'C', 'D', 'E', 'F', 'G'])
        ->and($epc->current_energy_efficiency)->toBeInt()
        ->and($epc->potential_energy_efficiency)->toBeInt()
        ->and($epc->fetched_at)->not->toBeNull();
});

test('epc data factory creates related property', function () {
    $epc = EpcData::factory()->create();

    expect($epc->property)->toBeInstanceOf(Property::class);
});

test('epc data potential efficiency is >= current efficiency', function () {
    $epc = EpcData::factory()->create();

    expect($epc->potential_energy_efficiency)->toBeGreaterThanOrEqual($epc->current_energy_efficiency);
});

// --- PlanningApplication Factory ---

test('planning application factory populates all required fields', function () {
    $app = PlanningApplication::factory()->create();

    expect($app->property_id)->toBeInt()
        ->and($app->reference)->toBeString()->not->toBeEmpty()
        ->and($app->status)->toBeIn(['Approved', 'Refused', 'Pending', 'Withdrawn', 'Appealed'])
        ->and($app->fetched_at)->not->toBeNull();
});

test('planning application factory creates related property', function () {
    $app = PlanningApplication::factory()->create();

    expect($app->property)->toBeInstanceOf(Property::class);
});

test('multiple planning applications can be created for same property', function () {
    $property = Property::factory()->create();
    $apps = PlanningApplication::factory()->count(5)->create(['property_id' => $property->id]);

    expect($apps)->toHaveCount(5);
});

// --- FloodRiskData Factory ---

test('flood risk data factory populates all required fields', function () {
    $flood = FloodRiskData::factory()->create();

    expect($flood->property_id)->toBeInt()
        ->and($flood->flood_risk_level)->toBeIn(['Very Low', 'Low', 'Medium', 'High'])
        ->and($flood->flood_zone)->toBeString()->not->toBeEmpty()
        ->and($flood->fetched_at)->not->toBeNull();
});

test('flood risk data factory creates related property', function () {
    $flood = FloodRiskData::factory()->create();

    expect($flood->property)->toBeInstanceOf(Property::class);
});

// --- CrimeData Factory ---

test('crime data factory populates all required fields', function () {
    $crime = CrimeData::factory()->create();

    expect($crime->property_id)->toBeInt()
        ->and($crime->month)->toMatch('/^\d{4}-\d{2}$/')
        ->and($crime->category)->toBeString()->not->toBeEmpty()
        ->and($crime->count)->toBeInt()->toBeGreaterThanOrEqual(0);
});

test('crime data factory creates related property', function () {
    $crime = CrimeData::factory()->create();

    expect($crime->property)->toBeInstanceOf(Property::class);
});

// --- LandRegistryData Factory ---

test('land registry data factory populates all required fields', function () {
    $registry = LandRegistryData::factory()->create();

    expect($registry->property_id)->toBeInt()
        ->and($registry->title_number)->toBeString()->not->toBeEmpty()
        ->and($registry->tenure)->toBeIn(['Freehold', 'Leasehold'])
        ->and($registry->last_sold_price)->toBeInt()
        ->and($registry->fetched_at)->not->toBeNull();
});

test('land registry data factory creates price history array', function () {
    $registry = LandRegistryData::factory()->create();

    expect($registry->price_history)->toBeArray()
        ->and($registry->price_history)->toHaveCount(2)
        ->and($registry->price_history[0])->toHaveKeys(['date', 'price'])
        ->and($registry->price_history[1])->toHaveKeys(['date', 'price']);
});

test('land registry data factory creates related property', function () {
    $registry = LandRegistryData::factory()->create();

    expect($registry->property)->toBeInstanceOf(Property::class);
});

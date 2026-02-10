<?php

use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\Property;
use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use App\Models\User;
use App\Services\ChecklistService;

beforeEach(function () {
    $this->service = new ChecklistService;
});

test('initializeChecklist creates assessment records for all items', function () {
    $saved = SavedProperty::factory()->create();

    $this->service->initializeChecklist($saved);

    $items = config('housescout.checklist.items', []);
    expect(PropertyAssessment::where('saved_property_id', $saved->id)->count())->toBe(count($items));
});

test('initializeChecklist does not duplicate records on second call', function () {
    $saved = SavedProperty::factory()->create();

    $this->service->initializeChecklist($saved);
    $this->service->initializeChecklist($saved);

    $items = config('housescout.checklist.items', []);
    expect(PropertyAssessment::where('saved_property_id', $saved->id)->count())->toBe(count($items));
});

test('autoAssess sets epc rating based on current energy rating', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'B',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->is_auto_assessed)->toBeTrue();
});

test('autoAssess sets flood risk based on level', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'flood_risk_level' => 'High',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'flood_risk')
        ->first();

    expect($assessment->assessment)->toBe('dislike')
        ->and($assessment->is_auto_assessed)->toBeTrue();
});

test('getProgress returns correct counts', function () {
    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'epc_rating',
        'assessment' => 'like',
    ]);
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'flood_risk',
        'assessment' => 'dislike',
    ]);
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'crime_level',
        'assessment' => null,
    ]);

    $progress = $this->service->getProgress($saved);

    expect($progress['assessed'])->toBe(2)
        ->and($progress['likes'])->toBe(1)
        ->and($progress['dislikes'])->toBe(1);
});

test('getProgress counts deal breakers correctly', function () {
    $saved = SavedProperty::factory()->create();

    // flood_risk is a deal-breaker in config
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'flood_risk',
        'assessment' => 'dislike',
    ]);

    $progress = $this->service->getProgress($saved);

    expect($progress['dealBreakers'])->toBe(1);
});

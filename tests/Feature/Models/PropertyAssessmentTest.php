<?php

use App\Models\PropertyAssessment;
use App\Models\SavedProperty;

test('property assessment belongs to saved property', function () {
    $assessment = PropertyAssessment::factory()->create();

    expect($assessment->savedProperty)->toBeInstanceOf(SavedProperty::class);
});

test('saved property has many assessments', function () {
    $saved = SavedProperty::factory()->create();
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'epc_rating']);
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'flood_risk']);
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'crime_level']);

    expect($saved->assessments)->toHaveCount(3);
});

test('assessment casts is_auto_assessed to boolean', function () {
    $assessment = PropertyAssessment::factory()->create(['is_auto_assessed' => true]);

    expect($assessment->is_auto_assessed)->toBeTrue()->toBeBool();
});

test('assessment casts auto_data to array', function () {
    $data = ['source' => 'epc', 'current_energy_rating' => 'B'];
    $assessment = PropertyAssessment::factory()->create(['auto_data' => $data]);

    $assessment->refresh();

    expect($assessment->auto_data)->toBeArray()
        ->and($assessment->auto_data['source'])->toBe('epc')
        ->and($assessment->auto_data['current_energy_rating'])->toBe('B');
});

test('assessment auto_data defaults to null', function () {
    $assessment = PropertyAssessment::factory()->create();

    expect($assessment->auto_data)->toBeNull();
});

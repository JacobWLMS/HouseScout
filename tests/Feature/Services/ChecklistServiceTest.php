<?php

use App\Models\ChecklistTemplate;
use App\Models\CrimeData;
use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\LandRegistryData;
use App\Models\PlanningApplication;
use App\Models\Property;
use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use App\Services\ChecklistService;

beforeEach(function () {
    $this->service = new ChecklistService;
});

function createTemplate(array $overrides = []): ChecklistTemplate
{
    return ChecklistTemplate::create(array_merge([
        'category' => 'test_category',
        'category_label' => 'Test Category',
        'key' => 'test_item_'.fake()->unique()->word(),
        'label' => 'Test Item',
        'severity' => ChecklistTemplate::IMPORTANT,
        'type' => ChecklistTemplate::MANUAL,
        'sort_order' => 0,
        'is_active' => true,
    ], $overrides));
}

function createTemplateSet(): array
{
    return [
        createTemplate(['key' => 'flood_zone', 'category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'severity' => ChecklistTemplate::DEAL_BREAKER, 'type' => ChecklistTemplate::AUTOMATED, 'sort_order' => 1]),
        createTemplate(['key' => 'flood_warnings', 'category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'severity' => ChecklistTemplate::DEAL_BREAKER, 'type' => ChecklistTemplate::AUTOMATED, 'sort_order' => 2]),
        createTemplate(['key' => 'epc_rating', 'category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'severity' => ChecklistTemplate::IMPORTANT, 'type' => ChecklistTemplate::AUTOMATED, 'sort_order' => 3]),
        createTemplate(['key' => 'heating_system', 'category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'severity' => ChecklistTemplate::IMPORTANT, 'type' => ChecklistTemplate::AUTOMATED, 'sort_order' => 4]),
        createTemplate(['key' => 'parking', 'category' => 'neighbourhood', 'category_label' => 'Neighbourhood', 'severity' => ChecklistTemplate::NICE_TO_HAVE, 'type' => ChecklistTemplate::MANUAL, 'sort_order' => 5]),
    ];
}

// --- initializeChecklist ---

test('initializeChecklist creates assessment records for all active templates', function () {
    $templates = createTemplateSet();
    $saved = SavedProperty::factory()->create();

    $this->service->initializeChecklist($saved);

    expect(PropertyAssessment::where('saved_property_id', $saved->id)->count())->toBe(count($templates));
});

test('initializeChecklist does not duplicate records on second call', function () {
    $templates = createTemplateSet();
    $saved = SavedProperty::factory()->create();

    $this->service->initializeChecklist($saved);
    $this->service->initializeChecklist($saved);

    expect(PropertyAssessment::where('saved_property_id', $saved->id)->count())->toBe(count($templates));
});

test('initializeChecklist ignores inactive templates', function () {
    createTemplate(['key' => 'active_item', 'is_active' => true]);
    createTemplate(['key' => 'inactive_item', 'is_active' => false]);

    $saved = SavedProperty::factory()->create();
    $this->service->initializeChecklist($saved);

    expect(PropertyAssessment::where('saved_property_id', $saved->id)->count())->toBe(1);
    expect(PropertyAssessment::where('item_key', 'active_item')->exists())->toBeTrue();
    expect(PropertyAssessment::where('item_key', 'inactive_item')->exists())->toBeFalse();
});

// --- autoAssess: EPC rules ---

test('autoAssess sets epc rating based on current energy rating', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'B',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->is_auto_assessed)->toBeTrue()
        ->and($assessment->auto_data)->toBeArray()
        ->and($assessment->auto_data['source'])->toBe('epc');
});

test('autoAssess sets epc dislike for poor rating', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'F',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBe('dislike');
});

test('autoAssess sets epc neutral for D rating', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'D',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBe('neutral');
});

test('autoAssess sets heating system based on description', function () {
    createTemplate(['key' => 'heating_system', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'main_heating_description' => 'Heat pump, radiators',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'heating_system')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['main_heating_description'])->toBe('Heat pump, radiators');
});

test('autoAssess sets heating system neutral for gas', function () {
    createTemplate(['key' => 'heating_system', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'main_heating_description' => 'Boiler and radiators, mains gas',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'heating_system')
        ->first();

    expect($assessment->assessment)->toBe('neutral');
});

test('autoAssess sets heating system dislike for electric', function () {
    createTemplate(['key' => 'heating_system', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'main_heating_description' => 'Electric storage heaters',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'heating_system')
        ->first();

    expect($assessment->assessment)->toBe('dislike');
});

test('autoAssess sets wall roof window efficiency based on gap', function () {
    createTemplate(['key' => 'wall_roof_window_efficiency', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_efficiency' => 70,
        'potential_energy_efficiency' => 75,
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'wall_roof_window_efficiency')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['gap'])->toBe(5);
});

test('autoAssess sets recommended improvements based on rating gap', function () {
    createTemplate(['key' => 'recommended_improvements', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'E',
        'potential_energy_rating' => 'B',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'recommended_improvements')
        ->first();

    expect($assessment->assessment)->toBe('dislike')
        ->and($assessment->auto_data['rating_gap'])->toBe(3);
});

// --- autoAssess: Flood rules ---

test('autoAssess sets flood zone based on zone data', function () {
    createTemplate(['key' => 'flood_zone', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'flood_zone' => 'Flood Zone 1',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'flood_zone')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['flood_zone'])->toBe('Flood Zone 1');
});

test('autoAssess sets flood zone dislike for zone 3', function () {
    createTemplate(['key' => 'flood_zone', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'flood_zone' => 'Flood Zone 3a',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'flood_zone')
        ->first();

    expect($assessment->assessment)->toBe('dislike');
});

test('autoAssess sets flood warnings based on active warnings', function () {
    createTemplate(['key' => 'flood_warnings', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'active_warnings' => ['warning1'],
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'flood_warnings')
        ->first();

    expect($assessment->assessment)->toBe('dislike')
        ->and($assessment->auto_data['active_warning_count'])->toBe(1);
});

test('autoAssess sets flood warnings like when none active', function () {
    createTemplate(['key' => 'flood_warnings', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'active_warnings' => [],
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'flood_warnings')
        ->first();

    expect($assessment->assessment)->toBe('like');
});

test('autoAssess sets surface water risk', function () {
    createTemplate(['key' => 'surface_water_risk', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    FloodRiskData::factory()->create([
        'property_id' => $property->id,
        'surface_water_risk' => 'High',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'surface_water_risk')
        ->first();

    expect($assessment->assessment)->toBe('dislike')
        ->and($assessment->auto_data['surface_water_risk'])->toBe('High');
});

// --- autoAssess: Crime rules ---

test('autoAssess sets overall crime level', function () {
    createTemplate(['key' => 'overall_crime_level', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    CrimeData::factory()->create([
        'property_id' => $property->id,
        'month' => '2024-01',
        'category' => 'burglary',
        'count' => 5,
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'overall_crime_level')
        ->first();

    expect($assessment->assessment)->toBe('like');
});

test('autoAssess sets burglary rate from burglary category', function () {
    createTemplate(['key' => 'burglary_rate', 'type' => ChecklistTemplate::AUTOMATED]);
    createTemplate(['key' => 'overall_crime_level', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    CrimeData::factory()->create([
        'property_id' => $property->id,
        'month' => '2024-01',
        'category' => 'burglary',
        'count' => 8,
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'burglary_rate')
        ->first();

    expect($assessment->assessment)->toBe('dislike');
});

test('autoAssess sets violent crime from violent-crime category', function () {
    createTemplate(['key' => 'violent_crime', 'type' => ChecklistTemplate::AUTOMATED]);
    createTemplate(['key' => 'overall_crime_level', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    CrimeData::factory()->create([
        'property_id' => $property->id,
        'month' => '2024-01',
        'category' => 'violent-crime',
        'count' => 2,
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'violent_crime')
        ->first();

    expect($assessment->assessment)->toBe('like');
});

// --- autoAssess: Planning rules ---

test('autoAssess sets nearby planning based on pending applications', function () {
    createTemplate(['key' => 'nearby_planning', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'status' => 'Pending',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'nearby_planning')
        ->first();

    expect($assessment->assessment)->toBe('neutral');
});

test('autoAssess sets nearby planning like when no pending', function () {
    createTemplate(['key' => 'nearby_planning', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'status' => 'Approved',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'nearby_planning')
        ->first();

    expect($assessment->assessment)->toBe('like');
});

test('autoAssess detects conservation area from planning applications', function () {
    createTemplate(['key' => 'conservation_area', 'type' => ChecklistTemplate::AUTOMATED]);
    createTemplate(['key' => 'nearby_planning', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'description' => 'Works in conservation area',
        'status' => 'Approved',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'conservation_area')
        ->first();

    expect($assessment->assessment)->toBe('neutral')
        ->and($assessment->auto_data['conservation_area_detected'])->toBeTrue();
});

test('autoAssess detects listed building from planning applications', function () {
    createTemplate(['key' => 'listed_building', 'type' => ChecklistTemplate::AUTOMATED]);
    createTemplate(['key' => 'nearby_planning', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    PlanningApplication::factory()->create([
        'property_id' => $property->id,
        'application_type' => 'Listed Building Consent',
        'status' => 'Approved',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'listed_building')
        ->first();

    expect($assessment->assessment)->toBe('neutral')
        ->and($assessment->auto_data['listed_building_detected'])->toBeTrue();
});

// --- autoAssess: Land Registry rules ---

test('autoAssess sets freehold leasehold from tenure', function () {
    createTemplate(['key' => 'freehold_leasehold', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    LandRegistryData::factory()->create([
        'property_id' => $property->id,
        'tenure' => 'Freehold',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'freehold_leasehold')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['tenure'])->toBe('Freehold');
});

test('autoAssess sets leasehold as neutral', function () {
    createTemplate(['key' => 'freehold_leasehold', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    LandRegistryData::factory()->create([
        'property_id' => $property->id,
        'tenure' => 'Leasehold',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'freehold_leasehold')
        ->first();

    expect($assessment->assessment)->toBe('neutral');
});

test('autoAssess sets previous sale prices when data exists', function () {
    createTemplate(['key' => 'previous_sale_prices', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    LandRegistryData::factory()->create([
        'property_id' => $property->id,
        'last_sold_price' => 250000,
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'previous_sale_prices')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['last_sold_price'])->toBe(250000);
});

test('autoAssess sets area price trend from price history', function () {
    createTemplate(['key' => 'area_price_trend', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    LandRegistryData::factory()->create([
        'property_id' => $property->id,
        'price_history' => [
            ['date' => '2015-01-01', 'price' => 200000],
            ['date' => '2023-01-01', 'price' => 300000],
        ],
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'area_price_trend')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->auto_data['trend'])->toBe('rising');
});

test('autoAssess sets price vs comparables', function () {
    createTemplate(['key' => 'price_vs_comparables', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    LandRegistryData::factory()->create([
        'property_id' => $property->id,
        'last_sold_price' => 300000,
        'price_history' => [
            ['date' => '2015-01-01', 'price' => 200000],
            ['date' => '2023-01-01', 'price' => 300000],
        ],
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);
    $this->service->initializeChecklist($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'price_vs_comparables')
        ->first();

    expect($assessment->assessment)->not->toBeNull()
        ->and($assessment->auto_data)->toHaveKey('ratio');
});

// --- autoAssess: does not overwrite manual assessment ---

test('autoAssess does not overwrite existing manual assessment', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'current_energy_rating' => 'F',
        'fetched_at' => now(),
    ]);

    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'epc_rating',
        'assessment' => 'like',
        'is_auto_assessed' => false,
    ]);

    $this->service->autoAssess($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBe('like')
        ->and($assessment->is_auto_assessed)->toBeFalse();
});

// --- getProgress ---

test('getProgress returns correct counts', function () {
    createTemplate(['key' => 'item_a', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'item_b', 'severity' => ChecklistTemplate::IMPORTANT]);
    createTemplate(['key' => 'item_c', 'severity' => ChecklistTemplate::NICE_TO_HAVE]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'item_a',
        'assessment' => 'like',
    ]);
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'item_b',
        'assessment' => 'dislike',
    ]);
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'item_c',
        'assessment' => null,
    ]);

    $progress = $this->service->getProgress($saved);

    expect($progress['total'])->toBe(3)
        ->and($progress['assessed'])->toBe(2)
        ->and($progress['likes'])->toBe(1)
        ->and($progress['dislikes'])->toBe(1)
        ->and($progress['neutral'])->toBe(0);
});

test('getProgress counts deal breakers correctly', function () {
    createTemplate(['key' => 'db_item', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp_item', 'severity' => ChecklistTemplate::IMPORTANT]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'db_item',
        'assessment' => 'dislike',
    ]);
    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'imp_item',
        'assessment' => 'dislike',
    ]);

    $progress = $this->service->getProgress($saved);

    expect($progress['dealBreakers'])->toBe(1);
});

test('getProgress returns severity aware stats', function () {
    createTemplate(['key' => 'db1', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'db2', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp1', 'severity' => ChecklistTemplate::IMPORTANT]);
    createTemplate(['key' => 'nth1', 'severity' => ChecklistTemplate::NICE_TO_HAVE]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'db1',
        'assessment' => 'like',
    ]);

    $progress = $this->service->getProgress($saved);

    expect($progress['deal_breaker_total'])->toBe(2)
        ->and($progress['deal_breaker_assessed'])->toBe(1)
        ->and($progress['important_total'])->toBe(1)
        ->and($progress['important_assessed'])->toBe(0)
        ->and($progress['nice_to_have_total'])->toBe(1)
        ->and($progress['nice_to_have_assessed'])->toBe(0);
});

// --- getWeightedScore ---

test('getWeightedScore calculates correct score with all likes', function () {
    createTemplate(['key' => 'db_item', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp_item', 'severity' => ChecklistTemplate::IMPORTANT]);
    createTemplate(['key' => 'nth_item', 'severity' => ChecklistTemplate::NICE_TO_HAVE]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'db_item', 'assessment' => 'like']);
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'imp_item', 'assessment' => 'like']);
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'nth_item', 'assessment' => 'like']);

    $score = $this->service->getWeightedScore($saved);

    expect($score['score'])->toBe(6.0)  // 3 + 2 + 1
        ->and($score['max'])->toBe(6.0)
        ->and($score['percentage'])->toBe(100.0);
});

test('getWeightedScore gives half points for neutral', function () {
    createTemplate(['key' => 'db_item', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp_item', 'severity' => ChecklistTemplate::IMPORTANT]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'db_item', 'assessment' => 'neutral']);
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'imp_item', 'assessment' => 'neutral']);

    $score = $this->service->getWeightedScore($saved);

    expect($score['score'])->toBe(2.5)  // 1.5 + 1.0
        ->and($score['max'])->toBe(5.0)  // 3 + 2
        ->and($score['percentage'])->toBe(50.0);
});

test('getWeightedScore gives zero for dislike and unassessed', function () {
    createTemplate(['key' => 'db_item', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp_item', 'severity' => ChecklistTemplate::IMPORTANT]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'db_item', 'assessment' => 'dislike']);
    // imp_item has no assessment

    $score = $this->service->getWeightedScore($saved);

    expect($score['score'])->toBe(0.0)
        ->and($score['max'])->toBe(5.0)
        ->and($score['percentage'])->toBe(0.0);
});

test('getWeightedScore returns zero when no templates', function () {
    $saved = SavedProperty::factory()->create();

    $score = $this->service->getWeightedScore($saved);

    expect($score['score'])->toBe(0.0)
        ->and($score['max'])->toBe(0.0)
        ->and($score['percentage'])->toBe(0.0);
});

test('getWeightedScore handles mixed assessments', function () {
    createTemplate(['key' => 'db1', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'db2', 'severity' => ChecklistTemplate::DEAL_BREAKER]);
    createTemplate(['key' => 'imp1', 'severity' => ChecklistTemplate::IMPORTANT]);
    createTemplate(['key' => 'nth1', 'severity' => ChecklistTemplate::NICE_TO_HAVE]);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'db1', 'assessment' => 'like']);     // 3
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'db2', 'assessment' => 'dislike']);  // 0
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'imp1', 'assessment' => 'neutral']); // 1
    PropertyAssessment::factory()->create(['saved_property_id' => $saved->id, 'item_key' => 'nth1', 'assessment' => 'like']);    // 1

    $score = $this->service->getWeightedScore($saved);

    // score = 3 + 0 + 1 + 1 = 5, max = 3 + 3 + 2 + 1 = 9
    expect($score['score'])->toBe(5.0)
        ->and($score['max'])->toBe(9.0)
        ->and($score['percentage'])->toBe(55.6);
});

// --- getGroupedChecklist ---

test('getGroupedChecklist returns templates grouped by category', function () {
    createTemplate(['key' => 'flood_a', 'category' => 'flood', 'category_label' => 'Flood & Environmental', 'sort_order' => 1]);
    createTemplate(['key' => 'flood_b', 'category' => 'flood', 'category_label' => 'Flood & Environmental', 'sort_order' => 2]);
    createTemplate(['key' => 'energy_a', 'category' => 'energy', 'category_label' => 'Energy & Condition', 'sort_order' => 3]);

    $saved = SavedProperty::factory()->create();
    $this->service->initializeChecklist($saved);

    $grouped = $this->service->getGroupedChecklist($saved);

    expect($grouped)->toHaveCount(2)
        ->and($grouped['flood']['category_label'])->toBe('Flood & Environmental')
        ->and($grouped['flood']['items'])->toHaveCount(2)
        ->and($grouped['energy']['category_label'])->toBe('Energy & Condition')
        ->and($grouped['energy']['items'])->toHaveCount(1);
});

test('getGroupedChecklist merges assessments with templates', function () {
    createTemplate(['key' => 'test_item', 'category' => 'test', 'category_label' => 'Test', 'severity' => ChecklistTemplate::IMPORTANT, 'type' => ChecklistTemplate::MANUAL, 'guidance' => 'Check this item']);

    $saved = SavedProperty::factory()->create();

    PropertyAssessment::factory()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'test_item',
        'assessment' => 'like',
        'is_auto_assessed' => false,
        'notes' => 'Looks good',
    ]);

    $grouped = $this->service->getGroupedChecklist($saved);

    $item = $grouped['test']['items']->first();
    expect($item['key'])->toBe('test_item')
        ->and($item['assessment'])->toBe('like')
        ->and($item['is_auto_assessed'])->toBeFalse()
        ->and($item['notes'])->toBe('Looks good')
        ->and($item['guidance'])->toBe('Check this item')
        ->and($item['severity'])->toBe(ChecklistTemplate::IMPORTANT);
});

test('getGroupedChecklist returns null assessment for unassessed items', function () {
    createTemplate(['key' => 'unassessed_item', 'category' => 'test', 'category_label' => 'Test']);

    $saved = SavedProperty::factory()->create();

    $grouped = $this->service->getGroupedChecklist($saved);

    $item = $grouped['test']['items']->first();
    expect($item['assessment'])->toBeNull()
        ->and($item['is_auto_assessed'])->toBeFalse();
});

// --- autoAssess with no property data ---

test('autoAssess returns early when property has no related data', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'epc_rating',
        'assessment' => null,
        'is_auto_assessed' => false,
    ]);

    // No EPC data, so autoAssess should leave the assessment as null
    $this->service->autoAssess($saved);

    $assessment = PropertyAssessment::where('saved_property_id', $saved->id)
        ->where('item_key', 'epc_rating')
        ->first();

    expect($assessment->assessment)->toBeNull();
});

test('autoAssess handles property with no API data', function () {
    createTemplate(['key' => 'epc_rating', 'type' => ChecklistTemplate::AUTOMATED]);
    createTemplate(['key' => 'flood_zone', 'type' => ChecklistTemplate::AUTOMATED]);

    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create(['property_id' => $property->id]);

    $this->service->initializeChecklist($saved);

    // All assessments should remain null since no API data exists
    $assessments = PropertyAssessment::where('saved_property_id', $saved->id)
        ->whereNotNull('assessment')
        ->count();

    expect($assessments)->toBe(0);
});

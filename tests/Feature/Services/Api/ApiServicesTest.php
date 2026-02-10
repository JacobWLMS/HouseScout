<?php

use App\Exceptions\ApiUnavailableException;
use App\Models\CrimeData;
use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\LandRegistryData;
use App\Models\PlanningApplication;
use App\Models\Property;
use App\Services\Api\EpcApiService;
use App\Services\Api\FloodMonitoringApiService;
use App\Services\Api\LandRegistryApiService;
use App\Services\Api\PlanningApiService;
use App\Services\Api\PoliceApiService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'housescout.api.epc.key' => 'test-api-key',
        'housescout.api.epc.email' => 'test@example.com',
        'housescout.api.epc.cache_ttl' => 86400,
        'housescout.api.planning.cache_ttl' => 86400,
        'housescout.api.flood.cache_ttl' => 3600,
        'housescout.api.police.cache_ttl' => 86400,
        'housescout.api.land_registry.cache_ttl' => 604800,
    ]);
    $this->property = Property::factory()->create([
        'postcode' => 'SW1A 1AA',
        'address_line_1' => '10 Downing Street',
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);
});

// =============================================
// PlanningApiService Tests
// =============================================

test('planning service creates planning applications from API response', function () {
    Http::fake([
        '*/entity.json*' => Http::response([
            'entities' => [
                [
                    'reference' => 'APP/2024/001',
                    'name' => 'Rear extension',
                    'planning-decision' => 'Approved',
                    'decision-date' => '2024-06-15',
                    'start-date' => '2024-01-10',
                    'planning-application-type' => 'Householder',
                ],
                [
                    'reference' => 'APP/2024/002',
                    'name' => 'Loft conversion',
                    'planning-decision' => 'Pending',
                    'start-date' => '2024-03-20',
                    'planning-application-type' => 'Full Planning',
                ],
            ],
        ]),
    ]);

    $service = new PlanningApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->planningApplications)->toHaveCount(2)
        ->and($this->property->planningApplications->first()->reference)->toBe('APP/2024/001')
        ->and($this->property->planningApplications->first()->status)->toBe('Approved');
});

test('planning service handles API failure gracefully', function () {
    Http::fake([
        '*/entity.json*' => Http::response(null, 500),
    ]);

    $service = new PlanningApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->planningApplications)->toHaveCount(0);
});

test('planning service handles empty entities response', function () {
    Http::fake([
        '*/entity.json*' => Http::response(['entities' => []]),
    ]);

    $service = new PlanningApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->planningApplications)->toHaveCount(0);
});

test('planning service skips when property has no coordinates', function () {
    Http::fake();
    $property = Property::factory()->create(['latitude' => null, 'longitude' => null]);

    $service = new PlanningApiService;
    $service->fetchForProperty($property);

    Http::assertNothingSent();
});

test('planning service throws ApiUnavailableException on connection error', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $service = new PlanningApiService;

    expect(fn () => $service->fetchForProperty($this->property))
        ->toThrow(ApiUnavailableException::class);
});

test('planning service isCacheStale returns true when no data exists', function () {
    $service = new PlanningApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('planning service isCacheStale returns true when data is older than TTL', function () {
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now()->subDays(2),
    ]);

    $service = new PlanningApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('planning service isCacheStale returns false when data is fresh', function () {
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $service = new PlanningApiService;

    expect($service->isCacheStale($this->property))->toBeFalse();
});

test('planning service skips fetch when cache is fresh', function () {
    Http::fake();
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $service = new PlanningApiService;
    $service->fetchForProperty($this->property);

    Http::assertNothingSent();
});

// =============================================
// FloodMonitoringApiService Tests
// =============================================

test('flood service creates flood risk data from API response', function () {
    Http::fake([
        '*/id/floods*' => Http::response([
            'items' => [
                [
                    'description' => 'River Thames flood warning',
                    'severityLevel' => 2,
                    'message' => 'Flooding is expected',
                ],
            ],
        ]),
    ]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($this->property);

    $floodData = $this->property->fresh()->floodRiskData;

    expect($floodData)->toBeInstanceOf(FloodRiskData::class)
        ->and($floodData->flood_risk_level)->toBe('high')
        ->and($floodData->active_warnings)->toHaveCount(1)
        ->and($floodData->fetched_at)->not->toBeNull();
});

test('flood service sets severe risk level for severity 1', function () {
    Http::fake([
        '*/id/floods*' => Http::response([
            'items' => [
                [
                    'description' => 'Severe flood warning',
                    'severityLevel' => 1,
                    'message' => 'Severe flooding',
                ],
            ],
        ]),
    ]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->fresh()->floodRiskData->flood_risk_level)->toBe('severe');
});

test('flood service sets low risk level when no items', function () {
    Http::fake([
        '*/id/floods*' => Http::response(['items' => []]),
    ]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->fresh()->floodRiskData->flood_risk_level)->toBe('low');
});

test('flood service sets moderate risk level for severity 3+', function () {
    Http::fake([
        '*/id/floods*' => Http::response([
            'items' => [
                [
                    'description' => 'Flood alert',
                    'severityLevel' => 3,
                    'message' => 'Minor flooding possible',
                ],
            ],
        ]),
    ]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->fresh()->floodRiskData->flood_risk_level)->toBe('moderate');
});

test('flood service handles API failure gracefully', function () {
    Http::fake([
        '*/id/floods*' => Http::response(null, 500),
    ]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->fresh()->floodRiskData)->toBeNull();
});

test('flood service skips when property has no coordinates', function () {
    Http::fake();
    $property = Property::factory()->create(['latitude' => null, 'longitude' => null]);

    $service = new FloodMonitoringApiService;
    $service->fetchForProperty($property);

    Http::assertNothingSent();
});

test('flood service throws ApiUnavailableException on connection error', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $service = new FloodMonitoringApiService;

    expect(fn () => $service->fetchForProperty($this->property))
        ->toThrow(ApiUnavailableException::class);
});

test('flood service isCacheStale returns true when no data exists', function () {
    $service = new FloodMonitoringApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('flood service isCacheStale returns true when data is older than TTL', function () {
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now()->subDays(2),
    ]);

    $service = new FloodMonitoringApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('flood service isCacheStale returns false when data is fresh', function () {
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $service = new FloodMonitoringApiService;

    expect($service->isCacheStale($this->property))->toBeFalse();
});

// =============================================
// PoliceApiService Tests
// =============================================

test('police service creates crime data from API response', function () {
    Http::fake([
        '*/crimes-at-location*' => Http::response([
            [
                'month' => '2024-01',
                'category' => 'burglary',
                'location' => ['latitude' => '51.5034', 'longitude' => '-0.1276'],
            ],
            [
                'month' => '2024-01',
                'category' => 'burglary',
                'location' => ['latitude' => '51.5034', 'longitude' => '-0.1276'],
            ],
            [
                'month' => '2024-01',
                'category' => 'anti-social-behaviour',
                'location' => ['latitude' => '51.5034', 'longitude' => '-0.1276'],
            ],
        ]),
    ]);

    $service = new PoliceApiService;
    $service->fetchForProperty($this->property);

    $crimeData = $this->property->crimeData;

    expect($crimeData)->toHaveCount(2);

    $burglary = $crimeData->where('category', 'burglary')->first();
    expect($burglary->count)->toBe(2)
        ->and($burglary->month)->toBe('2024-01');

    $asb = $crimeData->where('category', 'anti-social-behaviour')->first();
    expect($asb->count)->toBe(1);
});

test('police service handles API failure gracefully', function () {
    Http::fake([
        '*/crimes-at-location*' => Http::response(null, 500),
    ]);

    $service = new PoliceApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->crimeData)->toHaveCount(0);
});

test('police service handles empty crime list', function () {
    Http::fake([
        '*/crimes-at-location*' => Http::response([]),
    ]);

    $service = new PoliceApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->crimeData)->toHaveCount(0);
});

test('police service skips when property has no coordinates', function () {
    Http::fake();
    $property = Property::factory()->create(['latitude' => null, 'longitude' => null]);

    $service = new PoliceApiService;
    $service->fetchForProperty($property);

    Http::assertNothingSent();
});

test('police service throws ApiUnavailableException on connection error', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $service = new PoliceApiService;

    expect(fn () => $service->fetchForProperty($this->property))
        ->toThrow(ApiUnavailableException::class);
});

test('police service isCacheStale returns true when no data exists', function () {
    $service = new PoliceApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('police service isCacheStale returns true when data is older than TTL', function () {
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now()->subDays(2),
    ]);

    $service = new PoliceApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('police service isCacheStale returns false when data is fresh', function () {
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now(),
    ]);

    $service = new PoliceApiService;

    expect($service->isCacheStale($this->property))->toBeFalse();
});

// =============================================
// LandRegistryApiService Tests
// =============================================

test('land registry service creates data from API response', function () {
    Http::fake([
        '*/app/ppd*' => Http::response([
            'result' => [
                'items' => [
                    [
                        'date' => '2023-06-15',
                        'pricePaid' => 500000,
                        'propertyType' => 'Detached',
                    ],
                    [
                        'date' => '2015-03-20',
                        'pricePaid' => 350000,
                        'propertyType' => 'Detached',
                    ],
                ],
            ],
        ]),
    ]);

    $service = new LandRegistryApiService;
    $service->fetchForProperty($this->property);

    $landData = $this->property->fresh()->landRegistryData;

    expect($landData)->toBeInstanceOf(LandRegistryData::class)
        ->and($landData->last_sold_date->format('Y-m-d'))->toBe('2023-06-15')
        ->and($landData->last_sold_price)->toBe(500000)
        ->and($landData->price_history)->toHaveCount(2)
        ->and($landData->fetched_at)->not->toBeNull();
});

test('land registry service handles alternative response format', function () {
    Http::fake([
        '*/app/ppd*' => Http::response([
            'results' => [
                [
                    'transactionDate' => '2022-01-10',
                    'amount' => 450000,
                    'propertyType' => 'Semi-Detached',
                ],
            ],
        ]),
    ]);

    $service = new LandRegistryApiService;
    $service->fetchForProperty($this->property);

    $landData = $this->property->fresh()->landRegistryData;

    expect($landData)->not->toBeNull()
        ->and($landData->last_sold_date->format('Y-m-d'))->toBe('2022-01-10')
        ->and($landData->last_sold_price)->toBe(450000);
});

test('land registry service handles API failure gracefully', function () {
    Http::fake([
        '*/app/ppd*' => Http::response(null, 500),
    ]);

    $service = new LandRegistryApiService;
    $service->fetchForProperty($this->property);

    expect($this->property->fresh()->landRegistryData)->toBeNull();
});

test('land registry service handles empty results', function () {
    Http::fake([
        '*/app/ppd*' => Http::response(['result' => ['items' => []]]),
    ]);

    $service = new LandRegistryApiService;
    $service->fetchForProperty($this->property);

    $landData = $this->property->fresh()->landRegistryData;

    expect($landData)->not->toBeNull()
        ->and($landData->last_sold_price)->toBeNull()
        ->and($landData->price_history)->toBe([]);
});

test('land registry service throws ApiUnavailableException on connection error', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $service = new LandRegistryApiService;

    expect(fn () => $service->fetchForProperty($this->property))
        ->toThrow(ApiUnavailableException::class);
});

test('land registry service isCacheStale returns true when no data exists', function () {
    $service = new LandRegistryApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('land registry service isCacheStale returns true when data is older than TTL', function () {
    LandRegistryData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now()->subDays(10),
    ]);

    $service = new LandRegistryApiService;

    expect($service->isCacheStale($this->property))->toBeTrue();
});

test('land registry service isCacheStale returns false when data is fresh', function () {
    LandRegistryData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $service = new LandRegistryApiService;

    expect($service->isCacheStale($this->property))->toBeFalse();
});

test('land registry service skips fetch when cache is fresh', function () {
    Http::fake();
    LandRegistryData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $service = new LandRegistryApiService;
    $service->fetchForProperty($this->property);

    Http::assertNothingSent();
});

// =============================================
// EPC Service Additional Edge Cases
// =============================================

test('epc service updates existing data on re-fetch', function () {
    EpcData::factory()->create([
        'property_id' => $this->property->id,
        'current_energy_rating' => 'E',
        'fetched_at' => now()->subDays(2),
    ]);

    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'current-energy-rating' => 'C',
                    'potential-energy-rating' => 'B',
                    'current-energy-efficiency' => 69,
                    'potential-energy-efficiency' => 85,
                    'environment-impact-current' => 55,
                    'environment-impact-potential' => 70,
                    'energy-consumption-current' => 200,
                    'energy-consumption-potential' => 150,
                    'co2-emissions-current' => '3.5',
                    'co2-emiss-curr-per-floor-area' => '2.1',
                    'lighting-cost-current' => 100,
                    'lighting-cost-potential' => 80,
                    'heating-cost-current' => 500,
                    'heating-cost-potential' => 400,
                    'hot-water-cost-current' => 150,
                    'hot-water-cost-potential' => 120,
                    'mainheat-description' => 'Gas boiler',
                    'main-fuel' => 'Natural Gas',
                    'lodgement-date' => '2024-01-15',
                ],
            ],
        ]),
    ]);

    $service = new EpcApiService;
    $service->fetchForProperty($this->property);

    expect(EpcData::where('property_id', $this->property->id)->count())->toBe(1)
        ->and($this->property->fresh()->epcData->current_energy_rating)->toBe('C');
});

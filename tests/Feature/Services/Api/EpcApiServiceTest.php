<?php

use App\Exceptions\ApiUnavailableException;
use App\Models\EpcData;
use App\Models\Property;
use App\Services\Api\EpcApiService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['housescout.api.epc.key' => 'test-api-key']);
});

test('fetches and stores epc data for property', function () {
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

    $property = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $service = new EpcApiService;

    $service->fetchForProperty($property);

    $epc = $property->fresh()->epcData;

    expect($epc)->toBeInstanceOf(EpcData::class)
        ->and($epc->current_energy_rating)->toBe('C')
        ->and($epc->potential_energy_rating)->toBe('B')
        ->and($epc->current_energy_efficiency)->toBe(69)
        ->and($epc->main_heating_description)->toBe('Gas boiler')
        ->and($epc->fetched_at)->not->toBeNull();
});

test('skips fetch when cache is fresh', function () {
    Http::fake();

    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'fetched_at' => now(),
    ]);

    $service = new EpcApiService;
    $service->fetchForProperty($property);

    Http::assertNothingSent();
});

test('is cache stale returns true when no data exists', function () {
    $property = Property::factory()->create();
    $service = new EpcApiService;

    expect($service->isCacheStale($property))->toBeTrue();
});

test('is cache stale returns false when data is fresh', function () {
    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'fetched_at' => now(),
    ]);

    $service = new EpcApiService;

    expect($service->isCacheStale($property))->toBeFalse();
});

test('is cache stale returns true when data is expired', function () {
    $property = Property::factory()->create();
    EpcData::factory()->create([
        'property_id' => $property->id,
        'fetched_at' => now()->subDays(2),
    ]);

    $service = new EpcApiService;

    expect($service->isCacheStale($property))->toBeTrue();
});

test('handles api failure gracefully', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(null, 500),
    ]);

    $property = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $service = new EpcApiService;

    $service->fetchForProperty($property);

    expect($property->fresh()->epcData)->toBeNull();
});

test('handles empty api response', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(['rows' => []]),
    ]);

    $property = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $service = new EpcApiService;

    $service->fetchForProperty($property);

    expect($property->fresh()->epcData)->toBeNull();
});

test('throws ApiUnavailableException on connection error', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $property = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $service = new EpcApiService;

    expect(fn () => $service->fetchForProperty($property))
        ->toThrow(ApiUnavailableException::class);
});

test('skips fetch when api key is not configured', function () {
    Http::fake();
    config(['housescout.api.epc.key' => null]);

    $property = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $service = new EpcApiService;

    $service->fetchForProperty($property);

    Http::assertNothingSent();
});

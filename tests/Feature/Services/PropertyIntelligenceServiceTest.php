<?php

use App\Jobs\FetchCrimeDataJob;
use App\Jobs\FetchEpcDataJob;
use App\Jobs\FetchFloodRiskDataJob;
use App\Jobs\FetchLandRegistryDataJob;
use App\Jobs\FetchPlanningDataJob;
use App\Jobs\FetchPropertyDataJob;
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
use App\Services\PropertyIntelligenceService;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    config([
        'housescout.api.epc.cache_ttl' => 86400,
        'housescout.api.planning.cache_ttl' => 86400,
        'housescout.api.flood.cache_ttl' => 3600,
        'housescout.api.police.cache_ttl' => 86400,
        'housescout.api.land_registry.cache_ttl' => 604800,
    ]);
    $this->service = new PropertyIntelligenceService;
    $this->property = Property::factory()->create([
        'postcode' => 'SW1A 1AA',
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);
});

test('FetchPropertyDataJob can be dispatched for a property', function () {
    FetchPropertyDataJob::dispatch($this->property);

    Queue::assertPushed(FetchPropertyDataJob::class, function ($job) {
        return $job->property->id === $this->property->id;
    });
});

test('getStaleProviders returns all providers when no data exists', function () {
    $stale = $this->service->getStaleProviders($this->property);

    expect($stale)->toHaveCount(5)
        ->and($stale)->toContain(EpcApiService::class)
        ->and($stale)->toContain(PlanningApiService::class)
        ->and($stale)->toContain(FloodMonitoringApiService::class)
        ->and($stale)->toContain(PoliceApiService::class)
        ->and($stale)->toContain(LandRegistryApiService::class);
});

test('getStaleProviders returns empty when all data is fresh', function () {
    EpcData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now(),
    ]);
    LandRegistryData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $stale = $this->service->getStaleProviders($this->property);

    expect($stale)->toBeEmpty();
});

test('getStaleProviders returns only stale providers', function () {
    // Fresh data
    EpcData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    // Stale data (crime data older than TTL)
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now()->subDays(5),
    ]);

    // No land registry data at all = stale
    $stale = $this->service->getStaleProviders($this->property);

    expect($stale)->toContain(PoliceApiService::class)
        ->and($stale)->toContain(LandRegistryApiService::class)
        ->and($stale)->not->toContain(EpcApiService::class)
        ->and($stale)->not->toContain(PlanningApiService::class)
        ->and($stale)->not->toContain(FloodMonitoringApiService::class);
});

test('refreshStaleData dispatches only stale provider jobs', function () {
    // Fresh data for EPC, planning, flood
    EpcData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    // Stale crime data
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now()->subDays(5),
    ]);

    // No land registry data

    $this->service->refreshStaleData($this->property);

    Queue::assertPushed(FetchCrimeDataJob::class);
    Queue::assertPushed(FetchLandRegistryDataJob::class);
    Queue::assertNotPushed(FetchEpcDataJob::class);
    Queue::assertNotPushed(FetchPlanningDataJob::class);
    Queue::assertNotPushed(FetchFloodRiskDataJob::class);
});

test('refreshStaleData dispatches all jobs when everything is stale', function () {
    $this->service->refreshStaleData($this->property);

    Queue::assertPushed(FetchEpcDataJob::class);
    Queue::assertPushed(FetchPlanningDataJob::class);
    Queue::assertPushed(FetchFloodRiskDataJob::class);
    Queue::assertPushed(FetchCrimeDataJob::class);
    Queue::assertPushed(FetchLandRegistryDataJob::class);
});

test('refreshStaleData dispatches no jobs when everything is fresh', function () {
    EpcData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    PlanningApplication::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    FloodRiskData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);
    CrimeData::factory()->create([
        'property_id' => $this->property->id,
        'updated_at' => now(),
    ]);
    LandRegistryData::factory()->create([
        'property_id' => $this->property->id,
        'fetched_at' => now(),
    ]);

    $this->service->refreshStaleData($this->property);

    Queue::assertNotPushed(FetchEpcDataJob::class);
    Queue::assertNotPushed(FetchPlanningDataJob::class);
    Queue::assertNotPushed(FetchFloodRiskDataJob::class);
    Queue::assertNotPushed(FetchCrimeDataJob::class);
    Queue::assertNotPushed(FetchLandRegistryDataJob::class);
});

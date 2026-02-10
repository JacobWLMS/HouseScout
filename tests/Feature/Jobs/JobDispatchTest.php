<?php

use App\Jobs\FetchCrimeDataJob;
use App\Jobs\FetchEpcDataJob;
use App\Jobs\FetchFloodRiskDataJob;
use App\Jobs\FetchLandRegistryDataJob;
use App\Jobs\FetchPlanningDataJob;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Services\Api\EpcApiService;
use App\Services\Api\FloodMonitoringApiService;
use App\Services\Api\LandRegistryApiService;
use App\Services\Api\PlanningApiService;
use App\Services\Api\PoliceApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

// --- FetchPropertyDataJob ---

test('FetchPropertyDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();
    $job = new FetchPropertyDataJob($property);

    expect($job)->toBeInstanceOf(ShouldQueue::class);
});

test('FetchPropertyDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchPropertyDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

test('FetchPropertyDataJob dispatches exactly five sub-jobs', function () {
    Queue::fake();

    $property = Property::factory()->create();
    (new FetchPropertyDataJob($property))->handle();

    Queue::assertPushed(FetchEpcDataJob::class, 1);
    Queue::assertPushed(FetchPlanningDataJob::class, 1);
    Queue::assertPushed(FetchFloodRiskDataJob::class, 1);
    Queue::assertPushed(FetchCrimeDataJob::class, 1);
    Queue::assertPushed(FetchLandRegistryDataJob::class, 1);
});

// --- Individual fetch jobs implement ShouldQueue ---

test('FetchEpcDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();

    expect(new FetchEpcDataJob($property))->toBeInstanceOf(ShouldQueue::class);
});

test('FetchPlanningDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();

    expect(new FetchPlanningDataJob($property))->toBeInstanceOf(ShouldQueue::class);
});

test('FetchFloodRiskDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();

    expect(new FetchFloodRiskDataJob($property))->toBeInstanceOf(ShouldQueue::class);
});

test('FetchCrimeDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();

    expect(new FetchCrimeDataJob($property))->toBeInstanceOf(ShouldQueue::class);
});

test('FetchLandRegistryDataJob implements ShouldQueue', function () {
    $property = Property::factory()->create();

    expect(new FetchLandRegistryDataJob($property))->toBeInstanceOf(ShouldQueue::class);
});

// --- Individual fetch jobs store property ---

test('FetchEpcDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchEpcDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

test('FetchPlanningDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchPlanningDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

test('FetchFloodRiskDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchFloodRiskDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

test('FetchCrimeDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchCrimeDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

test('FetchLandRegistryDataJob stores property via constructor', function () {
    $property = Property::factory()->create();
    $job = new FetchLandRegistryDataJob($property);

    expect($job->property->id)->toBe($property->id);
});

// --- Individual fetch jobs call the correct service ---

test('FetchEpcDataJob calls EpcApiService', function () {
    $property = Property::factory()->create();
    $service = Mockery::mock(EpcApiService::class);
    $service->shouldReceive('fetchForProperty')->once()->with(
        Mockery::on(fn ($p) => $p->id === $property->id)
    );

    (new FetchEpcDataJob($property))->handle($service);
});

test('FetchPlanningDataJob calls PlanningApiService', function () {
    $property = Property::factory()->create();
    $service = Mockery::mock(PlanningApiService::class);
    $service->shouldReceive('fetchForProperty')->once()->with(
        Mockery::on(fn ($p) => $p->id === $property->id)
    );

    (new FetchPlanningDataJob($property))->handle($service);
});

test('FetchFloodRiskDataJob calls FloodMonitoringApiService', function () {
    $property = Property::factory()->create();
    $service = Mockery::mock(FloodMonitoringApiService::class);
    $service->shouldReceive('fetchForProperty')->once()->with(
        Mockery::on(fn ($p) => $p->id === $property->id)
    );

    (new FetchFloodRiskDataJob($property))->handle($service);
});

test('FetchCrimeDataJob calls PoliceApiService', function () {
    $property = Property::factory()->create();
    $service = Mockery::mock(PoliceApiService::class);
    $service->shouldReceive('fetchForProperty')->once()->with(
        Mockery::on(fn ($p) => $p->id === $property->id)
    );

    (new FetchCrimeDataJob($property))->handle($service);
});

test('FetchLandRegistryDataJob calls LandRegistryApiService', function () {
    $property = Property::factory()->create();
    $service = Mockery::mock(LandRegistryApiService::class);
    $service->shouldReceive('fetchForProperty')->once()->with(
        Mockery::on(fn ($p) => $p->id === $property->id)
    );

    (new FetchLandRegistryDataJob($property))->handle($service);
});

// --- Job serialization ---

test('FetchPropertyDataJob serializes and deserializes property', function () {
    $property = Property::factory()->create();
    $job = new FetchPropertyDataJob($property);

    $serialized = serialize($job);
    $unserialized = unserialize($serialized);

    expect($unserialized->property->id)->toBe($property->id);
});

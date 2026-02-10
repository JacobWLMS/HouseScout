<?php

use App\Jobs\FetchCrimeDataJob;
use App\Jobs\FetchEpcDataJob;
use App\Jobs\FetchFloodRiskDataJob;
use App\Jobs\FetchLandRegistryDataJob;
use App\Jobs\FetchPlanningDataJob;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use Illuminate\Support\Facades\Queue;

test('dispatches all five sub-jobs', function () {
    Queue::fake();

    $property = Property::factory()->create();
    $job = new FetchPropertyDataJob($property);

    $job->handle();

    Queue::assertPushed(FetchEpcDataJob::class, fn ($job) => $job->property->id === $property->id);
    Queue::assertPushed(FetchPlanningDataJob::class, fn ($job) => $job->property->id === $property->id);
    Queue::assertPushed(FetchFloodRiskDataJob::class, fn ($job) => $job->property->id === $property->id);
    Queue::assertPushed(FetchCrimeDataJob::class, fn ($job) => $job->property->id === $property->id);
    Queue::assertPushed(FetchLandRegistryDataJob::class, fn ($job) => $job->property->id === $property->id);
});

test('each sub-job receives the correct property', function () {
    Queue::fake();

    $property = Property::factory()->create();
    $job = new FetchPropertyDataJob($property);

    $job->handle();

    Queue::assertPushed(FetchEpcDataJob::class, function ($job) use ($property) {
        return $job->property->is($property);
    });
});

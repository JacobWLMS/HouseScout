<?php

namespace App\Jobs;

use App\Models\Property;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchPropertyDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(): void
    {
        FetchEpcDataJob::dispatch($this->property);
        FetchPlanningDataJob::dispatch($this->property);
        FetchFloodRiskDataJob::dispatch($this->property);
        FetchCrimeDataJob::dispatch($this->property);
        FetchLandRegistryDataJob::dispatch($this->property);
    }
}

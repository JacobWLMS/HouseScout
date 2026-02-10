<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Api\LandRegistryApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchLandRegistryDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(LandRegistryApiService $service): void
    {
        $service->fetchForProperty($this->property);
    }
}

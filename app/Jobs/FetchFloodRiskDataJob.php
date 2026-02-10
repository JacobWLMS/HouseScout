<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Api\FloodMonitoringApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchFloodRiskDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(FloodMonitoringApiService $service): void
    {
        $service->fetchForProperty($this->property);
    }
}

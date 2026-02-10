<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Api\PlanningApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchPlanningDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(PlanningApiService $service): void
    {
        $service->fetchForProperty($this->property);
    }
}

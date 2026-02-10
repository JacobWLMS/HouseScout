<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Api\PoliceApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchCrimeDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(PoliceApiService $service): void
    {
        $service->fetchForProperty($this->property);
    }
}

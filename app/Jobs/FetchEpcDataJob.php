<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Api\EpcApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchEpcDataJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Property $property,
    ) {}

    public function handle(EpcApiService $service): void
    {
        $service->fetchForProperty($this->property);
    }
}

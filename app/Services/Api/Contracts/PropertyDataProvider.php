<?php

namespace App\Services\Api\Contracts;

use App\Models\Property;

interface PropertyDataProvider
{
    public function fetchForProperty(Property $property): void;

    public function isCacheStale(Property $property): bool;
}

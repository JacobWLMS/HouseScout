<?php

namespace App\Services\Api;

use App\Exceptions\ApiUnavailableException;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PoliceApiService implements PropertyDataProvider
{
    public function fetchForProperty(Property $property): void
    {
        if (! $this->isCacheStale($property)) {
            return;
        }

        if (! $property->latitude || ! $property->longitude) {
            Log::warning('Police API skipped: missing coordinates', [
                'property_id' => $property->id,
            ]);

            return;
        }

        $baseUrl = config('housescout.api.police.base_url');

        try {
            $response = Http::accept('application/json')
                ->get("{$baseUrl}/crimes-at-location", [
                    'lat' => $property->latitude,
                    'lng' => $property->longitude,
                ]);

            if ($response->failed()) {
                Log::warning('Police API request failed', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                ]);

                return;
            }

            $crimes = $response->json();

            $grouped = collect($crimes)->groupBy(fn (array $crime) => ($crime['month'] ?? 'unknown').'|'.($crime['category'] ?? 'unknown'));

            foreach ($grouped as $key => $group) {
                [$month, $category] = explode('|', $key);

                $property->crimeData()->updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'month' => $month,
                        'category' => $category,
                    ],
                    [
                        'count' => $group->count(),
                    ],
                );
            }
        } catch (\Exception $e) {
            Log::warning('Police API unavailable', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiUnavailableException("Police API unavailable: {$e->getMessage()}", 0, $e);
        }
    }

    public function isCacheStale(Property $property): bool
    {
        $latestCrime = $property->crimeData()->latest('updated_at')->first();

        if (! $latestCrime) {
            return true;
        }

        $ttl = (int) config('housescout.api.police.cache_ttl');

        return $latestCrime->updated_at->addSeconds($ttl)->isPast();
    }
}

<?php

namespace App\Services\Api;

use App\Exceptions\ApiUnavailableException;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FloodMonitoringApiService implements PropertyDataProvider
{
    public function fetchForProperty(Property $property): void
    {
        if (! $this->isCacheStale($property)) {
            return;
        }

        if (! $property->latitude || ! $property->longitude) {
            Log::warning('Flood API skipped: missing coordinates', [
                'property_id' => $property->id,
            ]);

            return;
        }

        $baseUrl = config('housescout.api.flood.base_url');

        try {
            $response = Http::accept('application/json')
                ->get("{$baseUrl}/id/floods", [
                    'lat' => $property->latitude,
                    'long' => $property->longitude,
                    'dist' => 1,
                ]);

            if ($response->failed()) {
                Log::warning('Flood API request failed', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                ]);

                return;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];

            $activeWarnings = collect($items)->map(fn (array $item) => [
                'description' => $item['description'] ?? null,
                'severity' => $item['severityLevel'] ?? null,
                'message' => $item['message'] ?? null,
            ])->all();

            $floodRiskLevel = match (true) {
                collect($items)->contains(fn ($item) => ($item['severityLevel'] ?? 99) <= 1) => 'severe',
                collect($items)->contains(fn ($item) => ($item['severityLevel'] ?? 99) <= 2) => 'high',
                collect($items)->isNotEmpty() => 'moderate',
                default => 'low',
            };

            $property->floodRiskData()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'flood_risk_level' => $floodRiskLevel,
                    'active_warnings' => $activeWarnings,
                    'raw_response' => $data,
                    'fetched_at' => now(),
                ],
            );
        } catch (\Exception $e) {
            Log::warning('Flood API unavailable', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiUnavailableException("Flood API unavailable: {$e->getMessage()}", 0, $e);
        }
    }

    public function isCacheStale(Property $property): bool
    {
        $floodData = $property->floodRiskData;

        if (! $floodData || ! $floodData->fetched_at) {
            return true;
        }

        $ttl = (int) config('housescout.api.flood.cache_ttl');

        return $floodData->fetched_at->addSeconds($ttl)->isPast();
    }
}

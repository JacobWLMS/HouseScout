<?php

namespace App\Services\Api;

use App\Exceptions\ApiUnavailableException;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlanningApiService implements PropertyDataProvider
{
    public function fetchForProperty(Property $property): void
    {
        if (! $this->isCacheStale($property)) {
            return;
        }

        if (! $property->latitude || ! $property->longitude) {
            Log::warning('Planning API skipped: missing coordinates', [
                'property_id' => $property->id,
            ]);

            return;
        }

        $baseUrl = config('housescout.api.planning.base_url');

        try {
            $response = Http::accept('application/json')
                ->get("{$baseUrl}/entity.json", [
                    'geometry_reference' => 'point',
                    'longitude' => $property->longitude,
                    'latitude' => $property->latitude,
                    'dataset' => 'planning-application',
                ]);

            if ($response->failed()) {
                Log::warning('Planning API request failed', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                ]);

                return;
            }

            $data = $response->json();
            $entities = $data['entities'] ?? [];

            foreach ($entities as $entity) {
                $property->planningApplications()->updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'reference' => $entity['reference'] ?? $entity['entity'] ?? uniqid('plan_'),
                    ],
                    [
                        'description' => $entity['name'] ?? $entity['description'] ?? null,
                        'status' => $entity['planning-decision'] ?? null,
                        'decision_date' => $entity['decision-date'] ?? null,
                        'application_date' => $entity['start-date'] ?? null,
                        'application_type' => $entity['planning-application-type'] ?? null,
                        'raw_response' => $entity,
                        'fetched_at' => now(),
                    ],
                );
            }
        } catch (\Exception $e) {
            Log::warning('Planning API unavailable', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiUnavailableException("Planning API unavailable: {$e->getMessage()}", 0, $e);
        }
    }

    public function isCacheStale(Property $property): bool
    {
        $latestApplication = $property->planningApplications()->latest('fetched_at')->first();

        if (! $latestApplication || ! $latestApplication->fetched_at) {
            return true;
        }

        $ttl = config('housescout.api.planning.cache_ttl');

        return $latestApplication->fetched_at->addSeconds($ttl)->isPast();
    }
}

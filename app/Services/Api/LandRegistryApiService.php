<?php

namespace App\Services\Api;

use App\Exceptions\ApiUnavailableException;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LandRegistryApiService implements PropertyDataProvider
{
    public function fetchForProperty(Property $property): void
    {
        if (! $this->isCacheStale($property)) {
            return;
        }

        $baseUrl = config('housescout.api.land_registry.base_url');

        try {
            $response = Http::accept('application/json')
                ->get("{$baseUrl}/app/ppd", [
                    'postcode' => $property->postcode,
                ]);

            if ($response->failed()) {
                Log::warning('Land Registry API request failed', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                ]);

                return;
            }

            $data = $response->json();
            $results = $data['result']['items'] ?? $data['results'] ?? [];

            $priceHistory = collect($results)->map(fn (array $item) => [
                'date' => $item['date'] ?? $item['transactionDate'] ?? null,
                'price' => $item['pricePaid'] ?? $item['amount'] ?? null,
                'property_type' => $item['propertyType'] ?? null,
            ])->sortByDesc('date')->values()->all();

            $latest = $priceHistory[0] ?? null;

            $property->landRegistryData()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'last_sold_date' => $latest['date'] ?? null,
                    'last_sold_price' => $latest['price'] ?? null,
                    'price_history' => $priceHistory,
                    'raw_response' => $data,
                    'fetched_at' => now(),
                ],
            );
        } catch (\Exception $e) {
            Log::warning('Land Registry API unavailable', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiUnavailableException("Land Registry API unavailable: {$e->getMessage()}", 0, $e);
        }
    }

    public function isCacheStale(Property $property): bool
    {
        $landData = $property->landRegistryData;

        if (! $landData || ! $landData->fetched_at) {
            return true;
        }

        $ttl = config('housescout.api.land_registry.cache_ttl');

        return $landData->fetched_at->addSeconds($ttl)->isPast();
    }
}

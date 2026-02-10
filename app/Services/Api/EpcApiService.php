<?php

namespace App\Services\Api;

use App\Exceptions\ApiUnavailableException;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpcApiService implements PropertyDataProvider
{
    public function fetchForProperty(Property $property): void
    {
        if (! $this->isCacheStale($property)) {
            return;
        }

        $baseUrl = config('housescout.api.epc.base_url');
        $apiKey = config('housescout.api.epc.key');

        if (! $apiKey) {
            Log::warning('EPC API key not configured', [
                'property_id' => $property->id,
            ]);

            return;
        }

        try {
            $response = Http::withBasicAuth($apiKey, '')
                ->accept('application/json')
                ->get("{$baseUrl}/domestic/search", [
                    'postcode' => $property->postcode,
                    'address' => $property->address_line_1,
                ]);

            if ($response->failed()) {
                Log::warning('EPC API request failed', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                ]);

                return;
            }

            $data = $response->json();
            $row = $data['rows'][0] ?? null;

            if (! $row) {
                return;
            }

            $property->epcData()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'current_energy_rating' => $row['current-energy-rating'] ?? null,
                    'potential_energy_rating' => $row['potential-energy-rating'] ?? null,
                    'current_energy_efficiency' => $row['current-energy-efficiency'] ?? null,
                    'potential_energy_efficiency' => $row['potential-energy-efficiency'] ?? null,
                    'environment_impact_current' => $row['environment-impact-current'] ?? null,
                    'environment_impact_potential' => $row['environment-impact-potential'] ?? null,
                    'energy_consumption_current' => $row['energy-consumption-current'] ?? null,
                    'energy_consumption_potential' => $row['energy-consumption-potential'] ?? null,
                    'co2_emissions_current' => $row['co2-emissions-current'] ?? null,
                    'co2_emissions_potential' => $row['co2-emiss-curr-per-floor-area'] ?? null,
                    'lighting_cost_current' => $row['lighting-cost-current'] ?? null,
                    'lighting_cost_potential' => $row['lighting-cost-potential'] ?? null,
                    'heating_cost_current' => $row['heating-cost-current'] ?? null,
                    'heating_cost_potential' => $row['heating-cost-potential'] ?? null,
                    'hot_water_cost_current' => $row['hot-water-cost-current'] ?? null,
                    'hot_water_cost_potential' => $row['hot-water-cost-potential'] ?? null,
                    'main_heating_description' => $row['mainheat-description'] ?? null,
                    'main_fuel_type' => $row['main-fuel'] ?? null,
                    'lodgement_date' => $row['lodgement-date'] ?? null,
                    'raw_response' => $data,
                    'fetched_at' => now(),
                ],
            );
        } catch (\Exception $e) {
            Log::warning('EPC API unavailable', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiUnavailableException("EPC API unavailable: {$e->getMessage()}", 0, $e);
        }
    }

    public function isCacheStale(Property $property): bool
    {
        $epcData = $property->epcData;

        if (! $epcData || ! $epcData->fetched_at) {
            return true;
        }

        $ttl = config('housescout.api.epc.cache_ttl');

        return $epcData->fetched_at->addSeconds($ttl)->isPast();
    }
}

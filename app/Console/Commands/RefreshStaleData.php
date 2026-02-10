<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\PropertyIntelligenceService;
use Illuminate\Console\Command;

class RefreshStaleData extends Command
{
    protected $signature = 'app:refresh-stale-data';

    protected $description = 'Re-fetch stale cached data for all properties with existing data';

    public function handle(PropertyIntelligenceService $intelligenceService): int
    {
        $properties = Property::query()
            ->whereHas('epcData')
            ->orWhereHas('floodRiskData')
            ->orWhereHas('landRegistryData')
            ->orWhereHas('planningApplications')
            ->orWhereHas('crimeData')
            ->get();

        $refreshed = 0;

        foreach ($properties as $property) {
            $staleProviders = $intelligenceService->getStaleProviders($property);

            if (count($staleProviders) > 0) {
                $intelligenceService->refreshStaleData($property);
                $refreshed++;
            }
        }

        $this->info("Dispatched refresh jobs for {$refreshed} properties with stale data.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Services;

use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Services\Api\Contracts\PropertyDataProvider;
use App\Services\Api\EpcApiService;
use App\Services\Api\FloodMonitoringApiService;
use App\Services\Api\LandRegistryApiService;
use App\Services\Api\PlanningApiService;
use App\Services\Api\PoliceApiService;

class PropertyIntelligenceService
{
    /** @var list<class-string<PropertyDataProvider>> */
    private array $providers = [
        EpcApiService::class,
        PlanningApiService::class,
        FloodMonitoringApiService::class,
        PoliceApiService::class,
        LandRegistryApiService::class,
    ];

    public function fetchAllData(Property $property): void
    {
        FetchPropertyDataJob::dispatch($property);
    }

    /**
     * @return list<class-string<PropertyDataProvider>>
     */
    public function getStaleProviders(Property $property): array
    {
        $stale = [];

        foreach ($this->providers as $providerClass) {
            /** @var PropertyDataProvider $provider */
            $provider = app($providerClass);

            if ($provider->isCacheStale($property)) {
                $stale[] = $providerClass;
            }
        }

        return $stale;
    }

    public function refreshStaleData(Property $property): void
    {
        $staleProviders = $this->getStaleProviders($property);

        foreach ($staleProviders as $providerClass) {
            $jobClass = $this->getJobForProvider($providerClass);

            if ($jobClass) {
                $jobClass::dispatch($property);
            }
        }
    }

    /**
     * @param  class-string<PropertyDataProvider>  $providerClass
     * @return class-string|null
     */
    private function getJobForProvider(string $providerClass): ?string
    {
        return match ($providerClass) {
            EpcApiService::class => \App\Jobs\FetchEpcDataJob::class,
            PlanningApiService::class => \App\Jobs\FetchPlanningDataJob::class,
            FloodMonitoringApiService::class => \App\Jobs\FetchFloodRiskDataJob::class,
            PoliceApiService::class => \App\Jobs\FetchCrimeDataJob::class,
            LandRegistryApiService::class => \App\Jobs\FetchLandRegistryDataJob::class,
            default => null,
        };
    }
}

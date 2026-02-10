<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>Flood Risk</span>
            @include('filament.pages.partials.assessment-control', ['itemKey' => 'flood_risk', 'assessment' => $assessments['flood_risk'] ?? null, 'saved' => (bool) $this->savedProperty])
        </div>
    </x-slot>

    @if($this->property->floodRiskData && $this->property->floodRiskData->fetched_at)
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Risk Level:</span>
                <x-filament::badge size="lg" :color="match(strtolower($this->property->floodRiskData->flood_risk_level ?? '')) {
                    'high' => 'danger',
                    'medium' => 'warning',
                    'low', 'very low' => 'success',
                    default => 'gray',
                }">
                    {{ $this->property->floodRiskData->flood_risk_level ?? 'Unknown' }}
                </x-filament::badge>
            </div>

            @if($this->property->floodRiskData->surface_water_risk)
                <div>
                    <p class="text-xs text-gray-500">Surface Water: {{ $this->property->floodRiskData->surface_water_risk }}</p>
                </div>
            @endif

            @if($this->property->floodRiskData->active_warnings && count($this->property->floodRiskData->active_warnings) > 0)
                <div class="mt-2 border-t border-gray-200 pt-2 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-danger-600">Active Warnings</span>
                        @include('filament.pages.partials.assessment-control', ['itemKey' => 'flood_warnings', 'assessment' => $assessments['flood_warnings'] ?? null, 'saved' => (bool) $this->savedProperty])
                    </div>
                    @foreach($this->property->floodRiskData->active_warnings as $warning)
                        <p class="mt-1 text-xs text-danger-600">{{ is_string($warning) ? $warning : json_encode($warning) }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @elseif($this->property->floodRiskData)
        <div class="flex items-center justify-center py-4">
            <x-filament::loading-indicator class="mr-2 h-5 w-5" />
            <span class="text-sm text-gray-500">Fetching flood data...</span>
        </div>
    @else
        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No flood risk data available.</p>
    @endif
</x-filament::section>

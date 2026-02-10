<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>Energy Performance</span>
            @include('filament.pages.partials.assessment-control', ['itemKey' => 'epc_rating', 'assessment' => $assessments['epc_rating'] ?? null, 'saved' => (bool) $this->savedProperty])
        </div>
    </x-slot>

    @if($this->property->epcData && $this->property->epcData->fetched_at)
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Rating</p>
                <p class="text-3xl font-bold text-primary-600">{{ $this->property->epcData->current_energy_rating ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">Efficiency: {{ $this->property->epcData->current_energy_efficiency ?? 'N/A' }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Potential</p>
                <p class="text-3xl font-bold text-success-600">{{ $this->property->epcData->potential_energy_rating ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">Efficiency: {{ $this->property->epcData->potential_energy_efficiency ?? 'N/A' }}</p>
            </div>
        </div>

        @php
            $lightingCost = (int) ($this->property->epcData->lighting_cost_current ?? 0);
            $heatingCost = (int) ($this->property->epcData->heating_cost_current ?? 0);
            $hotWaterCost = (int) ($this->property->epcData->hot_water_cost_current ?? 0);
            $totalCost = $lightingCost + $heatingCost + $hotWaterCost;
        @endphp

        <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Running Costs</span>
                @include('filament.pages.partials.assessment-control', ['itemKey' => 'epc_costs', 'assessment' => $assessments['epc_costs'] ?? null, 'saved' => (bool) $this->savedProperty])
            </div>
            <div class="mt-2 grid grid-cols-3 gap-2 text-center text-xs">
                <div>
                    <p class="text-gray-500">Lighting</p>
                    <p class="font-medium">&pound;{{ $this->property->epcData->lighting_cost_current ?? 'N/A' }}/yr</p>
                </div>
                <div>
                    <p class="text-gray-500">Heating</p>
                    <p class="font-medium">&pound;{{ $this->property->epcData->heating_cost_current ?? 'N/A' }}/yr</p>
                </div>
                <div>
                    <p class="text-gray-500">Hot Water</p>
                    <p class="font-medium">&pound;{{ $this->property->epcData->hot_water_cost_current ?? 'N/A' }}/yr</p>
                </div>
            </div>
            @if($totalCost > 0)
                <p class="mt-2 text-center text-sm font-semibold text-gray-900 dark:text-white">Total: &pound;{{ number_format($totalCost) }}/yr</p>
            @endif
        </div>
    @elseif($this->property->epcData)
        <div class="flex items-center justify-center py-4">
            <x-filament::loading-indicator class="mr-2 h-5 w-5" />
            <span class="text-sm text-gray-500">Fetching EPC data...</span>
        </div>
    @else
        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No EPC data available.</p>
    @endif
</x-filament::section>

<x-filament-panels::page>
    {{-- Demand Badge --}}
    <div class="flex items-center gap-3 mb-6">
        <x-filament::badge color="primary" size="lg">
            {{ $this->demandCount }} {{ Str::plural('person', $this->demandCount) }} searched in last 30 days
        </x-filament::badge>
        @if($this->property->property_type)
            <x-filament::badge color="gray">
                {{ $this->property->property_type }}
            </x-filament::badge>
        @endif
    </div>

    {{-- Notes Section (only for saved properties) --}}
    @if($this->savedProperty)
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">Notes</x-slot>
            <form wire:submit="saveNotes" class="flex gap-3 items-end">
                <div class="flex-1">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model="notes"
                            placeholder="Add notes about this property..."
                        />
                    </x-filament::input.wrapper>
                </div>
                <x-filament::button type="submit" size="sm">
                    Save Notes
                </x-filament::button>
            </form>
        </x-filament::section>
    @endif

    {{-- Tabbed Content --}}
    <div x-data="{ tab: 'overview' }">
        <x-filament::tabs>
            <x-filament::tabs.item
                alpine-active="tab === 'overview'"
                x-on:click="tab = 'overview'"
            >
                Overview
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="tab === 'epc'"
                x-on:click="tab = 'epc'"
            >
                EPC
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="tab === 'planning'"
                x-on:click="tab = 'planning'"
            >
                Planning
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="tab === 'flood'"
                x-on:click="tab = 'flood'"
            >
                Flood Risk
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="tab === 'crime'"
                x-on:click="tab = 'crime'"
            >
                Crime
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="tab === 'land'"
                x-on:click="tab = 'land'"
            >
                Land Registry
            </x-filament::tabs.item>
        </x-filament::tabs>
        {{-- Overview --}}
        <div x-show="tab === 'overview'" x-cloak>
            <x-filament::section>
                <x-slot name="heading">Property Details</x-slot>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</p>
                        <p class="text-sm text-gray-950 dark:text-white">
                            {{ $this->property->address_line_1 }}
                            @if($this->property->address_line_2)
                                <br>{{ $this->property->address_line_2 }}
                            @endif
                            <br>{{ $this->property->city }}
                            @if($this->property->county)
                                <br>{{ $this->property->county }}
                            @endif
                            <br>{{ $this->property->postcode }}
                        </p>
                    </div>
                    <div class="space-y-3">
                        @if($this->property->property_type)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</p>
                                <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->property_type }}</p>
                            </div>
                        @endif
                        @if($this->property->built_form)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Built Form</p>
                                <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->built_form }}</p>
                            </div>
                        @endif
                        @if($this->property->floor_area)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Floor Area</p>
                                <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->floor_area }} m&sup2;</p>
                            </div>
                        @endif
                        @if($this->property->uprn)
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">UPRN</p>
                                <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->uprn }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- EPC --}}
        <div x-show="tab === 'epc'" x-cloak>
            @if($this->property->epcData)
                @if($this->property->epcData->fetched_at)
                    <x-filament::section>
                        <x-slot name="heading">Energy Performance Certificate</x-slot>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Rating</p>
                                <p class="text-4xl font-bold text-primary-600">{{ $this->property->epcData->current_energy_rating ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Efficiency: {{ $this->property->epcData->current_energy_efficiency ?? 'N/A' }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Potential Rating</p>
                                <p class="text-4xl font-bold text-success-600">{{ $this->property->epcData->potential_energy_rating ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Efficiency: {{ $this->property->epcData->potential_energy_efficiency ?? 'N/A' }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment Impact</p>
                                <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $this->property->epcData->environment_impact_current ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Potential: {{ $this->property->epcData->environment_impact_potential ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                            <x-filament::section>
                                <x-slot name="heading">Lighting Cost</x-slot>
                                <p class="text-sm">Current: &pound;{{ $this->property->epcData->lighting_cost_current ?? 'N/A' }}/yr</p>
                                <p class="text-sm">Potential: &pound;{{ $this->property->epcData->lighting_cost_potential ?? 'N/A' }}/yr</p>
                            </x-filament::section>
                            <x-filament::section>
                                <x-slot name="heading">Heating Cost</x-slot>
                                <p class="text-sm">Current: &pound;{{ $this->property->epcData->heating_cost_current ?? 'N/A' }}/yr</p>
                                <p class="text-sm">Potential: &pound;{{ $this->property->epcData->heating_cost_potential ?? 'N/A' }}/yr</p>
                            </x-filament::section>
                            <x-filament::section>
                                <x-slot name="heading">Hot Water Cost</x-slot>
                                <p class="text-sm">Current: &pound;{{ $this->property->epcData->hot_water_cost_current ?? 'N/A' }}/yr</p>
                                <p class="text-sm">Potential: &pound;{{ $this->property->epcData->hot_water_cost_potential ?? 'N/A' }}/yr</p>
                            </x-filament::section>
                        </div>

                        @if($this->property->epcData->main_heating_description || $this->property->epcData->main_fuel_type)
                            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                @if($this->property->epcData->main_heating_description)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Main Heating</p>
                                        <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->epcData->main_heating_description }}</p>
                                    </div>
                                @endif
                                @if($this->property->epcData->main_fuel_type)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Main Fuel Type</p>
                                        <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->epcData->main_fuel_type }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </x-filament::section>
                @else
                    <x-filament::section>
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-filament::loading-indicator class="mx-auto h-8 w-8 mb-4" />
                            <p>EPC data is being fetched...</p>
                        </div>
                    </x-filament::section>
                @endif
            @else
                <x-filament::section>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No EPC data available yet.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Planning --}}
        <div x-show="tab === 'planning'" x-cloak>
            @if($this->property->planningApplications->isNotEmpty())
                <x-filament::section>
                    <x-slot name="heading">Planning Applications</x-slot>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Reference</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Application Date</th>
                                    <th class="px-4 py-3">Decision Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->property->planningApplications as $application)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-medium">{{ $application->reference }}</td>
                                        <td class="px-4 py-3">{{ Str::limit($application->description, 60) }}</td>
                                        <td class="px-4 py-3">{{ $application->application_type ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <x-filament::badge :color="match(strtolower($application->status ?? '')) {
                                                'approved', 'granted', 'permitted' => 'success',
                                                'refused', 'rejected', 'declined' => 'danger',
                                                'pending', 'awaiting' => 'warning',
                                                default => 'gray',
                                            }">
                                                {{ $application->status ?? 'Unknown' }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-4 py-3">{{ $application->application_date?->format('d M Y') ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $application->decision_date?->format('d M Y') ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No planning applications found for this property.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Flood Risk --}}
        <div x-show="tab === 'flood'" x-cloak>
            @if($this->property->floodRiskData)
                @if($this->property->floodRiskData->fetched_at)
                    <x-filament::section>
                        <x-slot name="heading">Flood Risk Assessment</x-slot>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Overall Risk Level</p>
                                <x-filament::badge size="lg" :color="match(strtolower($this->property->floodRiskData->flood_risk_level ?? '')) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'success',
                                    'very low' => 'success',
                                    default => 'gray',
                                }">
                                    {{ $this->property->floodRiskData->flood_risk_level ?? 'Unknown' }}
                                </x-filament::badge>
                            </div>
                            @if($this->property->floodRiskData->flood_zone)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Flood Zone</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->floodRiskData->flood_zone }}</p>
                                </div>
                            @endif
                            @if($this->property->floodRiskData->river_and_sea_risk)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">River & Sea Risk</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->floodRiskData->river_and_sea_risk }}</p>
                                </div>
                            @endif
                            @if($this->property->floodRiskData->surface_water_risk)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Surface Water Risk</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->floodRiskData->surface_water_risk }}</p>
                                </div>
                            @endif
                            @if($this->property->floodRiskData->reservoir_risk)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reservoir Risk</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->floodRiskData->reservoir_risk }}</p>
                                </div>
                            @endif
                        </div>

                        @if($this->property->floodRiskData->active_warnings && count($this->property->floodRiskData->active_warnings) > 0)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-danger-600 mb-2">Active Flood Warnings</p>
                                <div class="space-y-2">
                                    @foreach($this->property->floodRiskData->active_warnings as $warning)
                                        <x-filament::section compact>
                                            <p class="text-sm text-danger-600">{{ is_string($warning) ? $warning : json_encode($warning) }}</p>
                                        </x-filament::section>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </x-filament::section>
                @else
                    <x-filament::section>
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-filament::loading-indicator class="mx-auto h-8 w-8 mb-4" />
                            <p>Flood risk data is being fetched...</p>
                        </div>
                    </x-filament::section>
                @endif
            @else
                <x-filament::section>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No flood risk data available yet.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Crime --}}
        <div x-show="tab === 'crime'" x-cloak>
            @if($this->property->crimeData->isNotEmpty())
                <x-filament::section>
                    <x-slot name="heading">Crime Statistics</x-slot>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Month</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->property->crimeData->sortByDesc('month') as $crime)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $crime->month }}</td>
                                        <td class="px-4 py-3">{{ Str::title(str_replace('-', ' ', $crime->category)) }}</td>
                                        <td class="px-4 py-3">
                                            <x-filament::badge color="gray">{{ $crime->count }}</x-filament::badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No crime data available for this area.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Land Registry --}}
        <div x-show="tab === 'land'" x-cloak>
            @if($this->property->landRegistryData)
                @if($this->property->landRegistryData->fetched_at)
                    <x-filament::section>
                        <x-slot name="heading">Land Registry Data</x-slot>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            @if($this->property->landRegistryData->title_number)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Title Number</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->landRegistryData->title_number }}</p>
                                </div>
                            @endif
                            @if($this->property->landRegistryData->tenure)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tenure</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->landRegistryData->tenure }}</p>
                                </div>
                            @endif
                            @if($this->property->landRegistryData->last_sold_date)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Sold Date</p>
                                    <p class="text-sm text-gray-950 dark:text-white">{{ $this->property->landRegistryData->last_sold_date->format('d M Y') }}</p>
                                </div>
                            @endif
                            @if($this->property->landRegistryData->last_sold_price)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Sold Price</p>
                                    <p class="text-sm text-gray-950 dark:text-white">&pound;{{ number_format($this->property->landRegistryData->last_sold_price) }}</p>
                                </div>
                            @endif
                        </div>

                        @if($this->property->landRegistryData->price_history && count($this->property->landRegistryData->price_history) > 0)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Price History</p>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                            <tr>
                                                <th class="px-4 py-3">Date</th>
                                                <th class="px-4 py-3">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->property->landRegistryData->price_history as $entry)
                                                <tr class="border-b dark:border-gray-700">
                                                    <td class="px-4 py-3">{{ $entry['date'] ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3">&pound;{{ number_format($entry['price'] ?? 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </x-filament::section>
                @else
                    <x-filament::section>
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-filament::loading-indicator class="mx-auto h-8 w-8 mb-4" />
                            <p>Land registry data is being fetched...</p>
                        </div>
                    </x-filament::section>
                @endif
            @else
                <x-filament::section>
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No land registry data available yet.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

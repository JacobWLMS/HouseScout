<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>Sales History</span>
            @include('filament.pages.partials.assessment-control', ['itemKey' => 'price_history', 'assessment' => $assessments['price_history'] ?? null, 'saved' => (bool) $this->savedProperty])
        </div>
    </x-slot>

    @if($this->property->landRegistryData && $this->property->landRegistryData->fetched_at)
        <div class="space-y-3">
            @if($this->property->landRegistryData->last_sold_price)
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">&pound;{{ number_format($this->property->landRegistryData->last_sold_price) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Last sold {{ $this->property->landRegistryData->last_sold_date?->format('M Y') ?? 'N/A' }}
                        @if($this->property->landRegistryData->tenure)
                            &middot; {{ $this->property->landRegistryData->tenure }}
                        @endif
                    </p>
                </div>
            @endif

            @if($this->property->landRegistryData->price_history && count($this->property->landRegistryData->price_history) > 0)
                <div class="space-y-1">
                    @foreach(collect($this->property->landRegistryData->price_history)->take(5) as $entry)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">{{ $entry['date'] ?? 'N/A' }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">&pound;{{ number_format($entry['price'] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @elseif($this->property->landRegistryData)
        <div class="flex items-center justify-center py-4">
            <x-filament::loading-indicator class="mr-2 h-5 w-5" />
            <span class="text-sm text-gray-500">Fetching sales data...</span>
        </div>
    @else
        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No sales data available.</p>
    @endif
</x-filament::section>

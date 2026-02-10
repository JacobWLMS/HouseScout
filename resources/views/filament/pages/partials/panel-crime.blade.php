<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>Crime Data</span>
            @include('filament.pages.partials.assessment-control', ['itemKey' => 'crime_level', 'assessment' => $assessments['crime_level'] ?? null, 'saved' => (bool) $this->savedProperty])
        </div>
    </x-slot>

    @if($this->property->crimeData->isNotEmpty())
        @php
            $totalCrimes = $this->property->crimeData->sum('count');
            $months = $this->property->crimeData->pluck('month')->unique()->count();
            $avgPerMonth = $months > 0 ? round($totalCrimes / $months) : 0;
            $topCategories = $this->property->crimeData->groupBy('category')->map->sum('count')->sortDesc()->take(3);
        @endphp

        <div class="space-y-3">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $avgPerMonth }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">incidents/month</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $totalCrimes }} total over {{ $months }} months</p>
                </div>
            </div>

            @if($topCategories->isNotEmpty())
                <div class="space-y-1">
                    @foreach($topCategories as $category => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">{{ Str::title(str_replace('-', ' ', $category)) }}</span>
                            <x-filament::badge color="gray" size="sm">{{ $count }}</x-filament::badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No crime data available.</p>
    @endif
</x-filament::section>

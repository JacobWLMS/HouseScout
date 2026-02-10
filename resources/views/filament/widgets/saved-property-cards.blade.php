<x-filament-widgets::widget>
    @php
        $savedProperties = $this->getSavedProperties();
    @endphp

    @if(count($savedProperties) > 0)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>Saved Properties</span>
                    @if(count($savedProperties) >= 2)
                        <a href="{{ $this->getCompareUrl() }}">
                            <x-filament::button size="sm" color="gray">
                                Compare ({{ count($savedProperties) }})
                            </x-filament::button>
                        </a>
                    @endif
                </div>
            </x-slot>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($savedProperties as $saved)
                    <a href="{{ $saved['url'] }}" class="block rounded-xl border border-gray-200 p-4 transition-shadow hover:shadow-md dark:border-gray-700">
                        {{-- Map Thumbnail --}}
                        @if($saved['property']->latitude && $saved['property']->longitude)
                            <img
                                class="mb-3 h-32 w-full rounded-lg border-0 object-cover"
                                loading="lazy"
                                alt="Map of {{ $saved['property']->address_line_1 }}"
                                src="https://staticmap.openstreetmap.de/staticmap.php?center={{ $saved['property']->latitude }},{{ $saved['property']->longitude }}&zoom=16&size=400x200&maptype=mapnik&markers={{ $saved['property']->latitude }},{{ $saved['property']->longitude }},red-pushpin"
                            >
                        @endif

                        {{-- Address --}}
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $saved['property']->address_line_1 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $saved['property']->postcode }}</p>

                        {{-- Weighted Score --}}
                        <div class="mt-3">
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Score</span>
                                @php
                                    $scorePct = $saved['weightedScore']['percentage'];
                                    $scoreColor = match(true) {
                                        $scorePct >= 70 => 'text-green-600 dark:text-green-400',
                                        $scorePct >= 40 => 'text-amber-600 dark:text-amber-400',
                                        default => 'text-red-600 dark:text-red-400',
                                    };
                                    $barColor = match(true) {
                                        $scorePct >= 70 => 'bg-green-500',
                                        $scorePct >= 40 => 'bg-amber-500',
                                        default => 'bg-red-500',
                                    };
                                @endphp
                                <span class="font-medium {{ $scoreColor }}">{{ $scorePct }}%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-1.5 rounded-full {{ $barColor }}" style="width: {{ $scorePct }}%"></div>
                            </div>
                        </div>

                        {{-- Severity Breakdown --}}
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            @if($saved['progress']['dealBreakers'] > 0)
                                <x-filament::badge color="danger" size="sm">
                                    {{ $saved['progress']['dealBreakers'] }} deal-breaker{{ $saved['progress']['dealBreakers'] > 1 ? 's' : '' }}
                                </x-filament::badge>
                            @endif
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $saved['progress']['deal_breaker_assessed'] }}/{{ $saved['progress']['deal_breaker_total'] }} critical
                            </span>
                            <span class="text-gray-400 dark:text-gray-500">&middot;</span>
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $saved['progress']['important_assessed'] }}/{{ $saved['progress']['important_total'] }} important
                            </span>
                            <span class="text-gray-400 dark:text-gray-500">&middot;</span>
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $saved['progress']['nice_to_have_assessed'] }}/{{ $saved['progress']['nice_to_have_total'] }} nice-to-have
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="py-8 text-center">
                <x-heroicon-o-bookmark class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No saved properties yet. Search for a property and save it to start your checklist.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>

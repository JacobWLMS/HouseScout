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
                            <iframe
                                class="mb-3 h-32 w-full rounded-lg border-0"
                                loading="lazy"
                                src="https://www.google.com/maps/embed/v1/place?key={{ config('housescout.api.google_maps.key') }}&q={{ urlencode($saved['property']->address_line_1 . ', ' . $saved['property']->postcode) }}&maptype=satellite&zoom=16"
                            ></iframe>
                        @endif

                        {{-- Address --}}
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $saved['property']->address_line_1 }}</p>
                        <p class="text-sm text-gray-500">{{ $saved['property']->postcode }}</p>

                        {{-- Progress Bar --}}
                        <div class="mt-3">
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="text-gray-500">{{ $saved['progress']['assessed'] }}/{{ $saved['progress']['total'] }}</span>
                                <span class="font-medium">{{ $saved['progress']['percentage'] }}%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-1.5 rounded-full bg-primary-600" style="width: {{ $saved['progress']['percentage'] }}%"></div>
                            </div>
                        </div>

                        {{-- Score Summary --}}
                        <div class="mt-2 flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-0.5 text-green-600">
                                <x-heroicon-s-hand-thumb-up class="h-3 w-3" />
                                {{ $saved['progress']['likes'] }}
                            </span>
                            <span class="flex items-center gap-0.5 text-red-600">
                                <x-heroicon-s-hand-thumb-down class="h-3 w-3" />
                                {{ $saved['progress']['dislikes'] }}
                            </span>
                            @if($saved['progress']['dealBreakers'] > 0)
                                <x-filament::badge color="danger" size="sm">
                                    {{ $saved['progress']['dealBreakers'] }} deal-breaker{{ $saved['progress']['dealBreakers'] > 1 ? 's' : '' }}
                                </x-filament::badge>
                            @else
                                <span class="text-green-600">No issues</span>
                            @endif
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

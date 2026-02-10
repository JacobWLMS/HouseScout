<div x-data="{ view: 'satellite' }">
    <x-filament::section>
        <x-slot name="heading">Location</x-slot>

        {{-- View Toggle --}}
        <div class="mb-3 flex gap-2">
            <button
                type="button"
                x-on:click="view = 'satellite'"
                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm transition-colors"
                x-bind:class="view === 'satellite' ? 'bg-primary-600 text-white hover:bg-primary-500' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700'"
            >
                Satellite
            </button>
            <button
                type="button"
                x-on:click="view = 'streetview'"
                class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm transition-colors"
                x-bind:class="view === 'streetview' ? 'bg-primary-600 text-white hover:bg-primary-500' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700'"
            >
                Street View
            </button>
        </div>

        {{-- Satellite View --}}
        <div x-show="view === 'satellite'" x-cloak>
            <iframe
                class="h-[400px] w-full rounded-lg border border-gray-200 dark:border-gray-700"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/place?key={{ config('housescout.api.google_maps.key') }}&q={{ urlencode($address) }}&maptype=satellite"
            ></iframe>
        </div>

        {{-- Street View --}}
        <div x-show="view === 'streetview'" x-cloak>
            <iframe
                class="h-[400px] w-full rounded-lg border border-gray-200 dark:border-gray-700"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/streetview?key={{ config('housescout.api.google_maps.key') }}&location={{ $latitude }},{{ $longitude }}"
            ></iframe>
        </div>
    </x-filament::section>
</div>

<div>
    <x-filament::section>
        <x-slot name="heading">Location</x-slot>

        <div x-data="{ activeView: 'satellite' }">
            {{-- View Toggle --}}
            <div class="mb-3 flex gap-2">
                <button
                    type="button"
                    x-on:click="activeView = 'satellite'"
                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm transition-colors"
                    x-bind:class="activeView === 'satellite' ? 'bg-primary-600 text-white hover:bg-primary-500' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700'"
                >
                    Satellite
                </button>
                <button
                    type="button"
                    x-on:click="activeView = 'streetview'"
                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm transition-colors"
                    x-bind:class="activeView === 'streetview' ? 'bg-primary-600 text-white hover:bg-primary-500' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700'"
                >
                    Street View
                </button>
            </div>

            {{-- Google Maps Embeds --}}
            <div x-show="activeView === 'satellite'" x-cloak>
                <iframe
                    src="{{ $this->satelliteMapUrl() }}"
                    class="aspect-video w-full rounded-lg border border-gray-200 dark:border-gray-700"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
            <div x-show="activeView === 'streetview'" x-cloak>
                <iframe
                    src="{{ $this->streetViewUrl() }}"
                    class="aspect-video w-full rounded-lg border border-gray-200 dark:border-gray-700"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </x-filament::section>
</div>

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                Search for a Property
            </h2>

            <div
                x-data="{ open: @entangle('showDropdown') }"
                @click.outside="open = false; $wire.closeDropdown()"
                class="relative"
            >
                {{-- Search Input --}}
                <div class="relative">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model.live.debounce.300ms="query"
                            placeholder="Start typing a postcode (e.g. SW1A)..."
                            autocomplete="off"
                            @focus="if ($wire.postcodeSuggestions.length > 0 || $wire.addresses.length > 0) open = true"
                        />
                    </x-filament::input.wrapper>

                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="selectPostcode" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="size-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Dropdown --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                    style="display: none;"
                >
                    {{-- Step 1: Postcode Suggestions --}}
                    @if(!empty($postcodeSuggestions) && empty($addresses))
                        <ul class="max-h-60 overflow-y-auto py-1">
                            @foreach($postcodeSuggestions as $suggestion)
                                <li>
                                    <button
                                        wire:click="selectPostcode('{{ $suggestion }}')"
                                        type="button"
                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span>{{ $suggestion }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Step 2: Address List --}}
                    @if(!empty($addresses))
                        <div class="border-b border-gray-100 px-4 py-2.5 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">
                                    {{ count($addresses) }} {{ Str::plural('address', count($addresses)) }} at {{ $selectedPostcode }}
                                </p>
                                <button
                                    wire:click="resetSearch"
                                    type="button"
                                    class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400"
                                >
                                    New search
                                </button>
                            </div>
                        </div>
                        <ul class="max-h-72 overflow-y-auto py-1">
                            @foreach($addresses as $index => $address)
                                <li>
                                    <button
                                        wire:click="selectAddress({{ $index }})"
                                        type="button"
                                        class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-4 text-gray-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $address['address_line_1'] }}
                                            </p>
                                            @if($address['address_line_2'])
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $address['address_line_2'] }}
                                                </p>
                                            @endif
                                            <div class="mt-1 flex items-center gap-2">
                                                @if($address['property_type'])
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $address['property_type'] }}
                                                    </span>
                                                @endif
                                                @if($address['energy_rating'])
                                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium
                                                        {{ match($address['energy_rating']) {
                                                            'A', 'B' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                            'C' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                            'D' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                            'E' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                                            'F', 'G' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                            default => 'bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                                        } }}
                                                    ">
                                                        EPC {{ $address['energy_rating'] }}
                                                    </span>
                                                @endif
                                                @if($address['floor_area'])
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $address['floor_area'] }}m&sup2;
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-4 text-gray-300 shrink-0 dark:text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Loading state --}}
                    @if($isLoadingAddresses)
                        <div class="flex items-center justify-center py-8">
                            <svg class="size-6 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Loading addresses...</span>
                        </div>
                    @endif

                    {{-- Empty state for suggestions --}}
                    @if(empty($postcodeSuggestions) && empty($addresses) && !$isLoadingAddresses && strlen(trim($query)) >= 2 && !$selectedPostcode)
                        <div class="px-4 py-6 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No postcodes found matching "{{ $query }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Type a postcode to see suggestions, then select your address from the list.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                Search for a Property
            </h2>
            <form wire:submit="search" class="flex gap-3">
                <div class="flex-1">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model="query"
                            placeholder="Enter a postcode or address (e.g. SW1A 1AA)"
                        />
                    </x-filament::input.wrapper>
                    @error('query')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="search">Search</span>
                    <span wire:loading wire:target="search">Searching...</span>
                </x-filament::button>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

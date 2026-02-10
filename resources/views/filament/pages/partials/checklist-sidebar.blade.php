<div class="space-y-4">
    {{-- Progress --}}
    <x-filament::section>
        <x-slot name="heading">Checklist Progress</x-slot>

        @php
            $progress = $this->checklistProgress;
            $percentage = $progress['percentage'] ?? 0;
        @endphp

        <div class="space-y-3">
            {{-- Progress Bar --}}
            <div>
                <div class="mb-1 flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ $progress['assessed'] ?? 0 }} / {{ $progress['total'] ?? 0 }} assessed</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $percentage }}%</span>
                </div>
                <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-2.5 rounded-full bg-primary-600 transition-all" style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            {{-- Score Summary --}}
            <div class="flex items-center gap-3 text-sm">
                <span class="flex items-center gap-1 text-green-600">
                    <x-heroicon-s-hand-thumb-up class="h-4 w-4" />
                    {{ $progress['likes'] ?? 0 }}
                </span>
                <span class="flex items-center gap-1 text-red-600">
                    <x-heroicon-s-hand-thumb-down class="h-4 w-4" />
                    {{ $progress['dislikes'] ?? 0 }}
                </span>
                <span class="flex items-center gap-1 text-yellow-600">
                    <x-heroicon-s-minus-circle class="h-4 w-4" />
                    {{ $progress['neutral'] ?? 0 }}
                </span>
            </div>

            {{-- Deal-breakers --}}
            @if(($progress['dealBreakers'] ?? 0) > 0)
                <div class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400">Deal-breakers: {{ $progress['dealBreakers'] }}</p>
                    @foreach($this->getDealBreakerItems() as $item)
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                            &bull; {{ $item['label'] }}
                        </p>
                    @endforeach
                </div>
            @endif

            {{-- Unassessed Count --}}
            @php $unassessed = ($progress['total'] ?? 0) - ($progress['assessed'] ?? 0); @endphp
            @if($unassessed > 0)
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $unassessed }} items still to assess</p>
            @endif
        </div>
    </x-filament::section>

    {{-- Notes --}}
    <x-filament::section>
        <x-slot name="heading">Notes</x-slot>
        <form wire:submit="saveNotes" class="space-y-2">
            <x-filament::input.wrapper>
                <textarea
                    wire:model="notes"
                    rows="3"
                    placeholder="Add notes about this property..."
                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-sm text-gray-950 placeholder-gray-400 focus:ring-0 dark:text-white"
                ></textarea>
            </x-filament::input.wrapper>
            <x-filament::button type="submit" size="sm">
                Save Notes
            </x-filament::button>
        </form>
    </x-filament::section>

    {{-- Compare Button --}}
    @php
        $savedCount = \App\Models\SavedProperty::where('user_id', auth()->id())->count();
    @endphp
    @if($savedCount >= 2)
        <a href="{{ \App\Filament\Pages\ComparePropertiesPage::getUrl() }}" class="block">
            <x-filament::button color="gray" class="w-full">
                Compare Properties ({{ $savedCount }})
            </x-filament::button>
        </a>
    @endif
</div>

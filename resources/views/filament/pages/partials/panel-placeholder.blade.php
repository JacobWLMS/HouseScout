@props(['title' => 'Coming Soon', 'itemKey' => null])

<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>{{ $title }}</span>
            @if($itemKey)
                @include('filament.pages.partials.assessment-control', ['itemKey' => $itemKey, 'assessment' => $assessments[$itemKey] ?? null, 'saved' => (bool) $this->savedProperty])
            @endif
        </div>
    </x-slot>

    <div class="py-6 text-center">
        <x-heroicon-o-clock class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Data coming soon</p>
    </div>
</x-filament::section>

@props(['itemKey', 'assessment' => null, 'saved' => false])

@if($saved)
<div class="flex items-center gap-1">
    <button
        type="button"
        wire:click="assessItem('{{ $itemKey }}', 'like')"
        class="rounded-full p-1 transition-colors {{ $assessment === 'like' ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-400 hover:text-green-500 dark:text-gray-500 dark:hover:text-green-400' }}"
        title="Good"
    >
        <x-heroicon-s-hand-thumb-up class="h-5 w-5" />
    </button>
    <button
        type="button"
        wire:click="assessItem('{{ $itemKey }}', 'neutral')"
        class="rounded-full p-1 transition-colors {{ $assessment === 'neutral' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'text-gray-400 hover:text-yellow-500 dark:text-gray-500 dark:hover:text-yellow-400' }}"
        title="Neutral"
    >
        <x-heroicon-s-minus-circle class="h-5 w-5" />
    </button>
    <button
        type="button"
        wire:click="assessItem('{{ $itemKey }}', 'dislike')"
        class="rounded-full p-1 transition-colors {{ $assessment === 'dislike' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400' }}"
        title="Concern"
    >
        <x-heroicon-s-hand-thumb-down class="h-5 w-5" />
    </button>
    @if($assessment)
        <button
            type="button"
            wire:click="removeAssessment('{{ $itemKey }}')"
            class="rounded-full p-1 text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400"
            title="Clear"
        >
            <x-heroicon-o-x-mark class="h-4 w-4" />
        </button>
    @endif
</div>
@endif

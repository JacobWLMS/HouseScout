<div class="space-y-4">
    {{-- Weighted Score Header --}}
    <x-filament::section>
        <x-slot name="heading">Property Score</x-slot>

        @php
            $score = $this->weightedScore;
            $progress = $this->checklistProgress;
            $percentage = $score['percentage'] ?? 0;
            $assessed = $progress['assessed'] ?? 0;
            $total = $progress['total'] ?? 0;
        @endphp

        <div class="space-y-3">
            {{-- Score Circle + Percentage --}}
            <div class="flex items-center gap-4">
                <div class="relative h-16 w-16 shrink-0">
                    <svg class="h-16 w-16 -rotate-90" viewBox="0 0 36 36">
                        <path
                            class="text-gray-200 dark:text-gray-700"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                        />
                        <path
                            class="{{ $percentage >= 70 ? 'text-emerald-500' : ($percentage >= 40 ? 'text-amber-500' : 'text-red-500') }}"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-dasharray="{{ $percentage }}, 100"
                        />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-900 dark:text-white">
                        {{ number_format($percentage, 0) }}%
                    </span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Weighted Score</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($score['score'] ?? 0, 1) }} / {{ number_format($score['max'] ?? 0, 1) }} points</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div>
                <div class="mb-1 flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ $assessed }} / {{ $total }} assessed</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $progress['percentage'] ?? 0 }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 rounded-full bg-primary-600 transition-all" style="width: {{ $progress['percentage'] ?? 0 }}%"></div>
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
        </div>
    </x-filament::section>

    {{-- Deal-Breaker Alert --}}
    @if(($progress['dealBreakers'] ?? 0) > 0)
        <div class="rounded-lg border border-red-300 bg-red-50 p-3 dark:border-red-700 dark:bg-red-900/20">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">
                {{ $progress['dealBreakers'] }} Deal-breaker {{ Str::plural('issue', $progress['dealBreakers']) }}
            </p>
            @foreach($this->getDealBreakerItems() as $item)
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                    &bull; {{ $item['label'] }}
                </p>
            @endforeach
        </div>
    @endif

    {{-- Category Sections --}}
    @foreach($this->checklistGroups as $category => $group)
        @php
            $items = $group['items'] ?? [];
            $categoryLabel = $group['category_label'] ?? $category;
            $assessedInCategory = collect($items)->filter(fn ($i) => ($i['assessment'] ?? null) !== null)->count();
            $totalInCategory = count($items);
        @endphp

        <x-filament::section>
            <div x-data="{ open: true }">
                {{-- Category Header --}}
                <button
                    type="button"
                    x-on:click="open = !open"
                    class="flex w-full items-center justify-between text-left"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $categoryLabel }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $assessedInCategory }}/{{ $totalInCategory }}</span>
                    </div>
                    <x-heroicon-o-chevron-down
                        class="h-4 w-4 text-gray-400 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''"
                    />
                </button>

                {{-- Items --}}
                <div x-show="open" x-collapse class="mt-3 space-y-3">
                    @foreach($items as $item)
                        @php
                            $severityColor = match($item['severity'] ?? '') {
                                'deal_breaker' => 'bg-red-500',
                                'important' => 'bg-amber-500',
                                default => 'bg-gray-400',
                            };
                            $itemAssessment = $item['assessment'] ?? null;
                        @endphp

                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700" x-data="{ showNotes: false }">
                            {{-- Item Header --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2 w-2 shrink-0 rounded-full {{ $severityColor }}"></span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                                        @if($item['is_auto_assessed'] ?? false)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Auto</span>
                                        @endif
                                        @if($item['link'] ?? null)
                                            <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-500">
                                                <x-heroicon-o-arrow-top-right-on-square class="h-3.5 w-3.5" />
                                            </a>
                                        @endif
                                    </div>

                                    {{-- Guidance --}}
                                    @if($item['guidance'] ?? null)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item['guidance'] }}</p>
                                    @endif

                                    {{-- Auto Data Summary --}}
                                    @if(!empty($item['auto_data']) && is_array($item['auto_data']))
                                        @php
                                            $autoSummary = collect($item['auto_data'])
                                                ->except('source')
                                                ->map(fn ($v, $k) => Str::headline($k) . ': ' . (is_array($v) ? json_encode($v) : $v))
                                                ->implode(' | ');
                                        @endphp
                                        @if($autoSummary)
                                            <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">{{ Str::limit($autoSummary, 80) }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Verdict Buttons --}}
                            <div class="mt-2 flex items-center gap-1">
                                @include('filament.pages.partials.assessment-control', [
                                    'itemKey' => $item['key'],
                                    'assessment' => $itemAssessment,
                                    'saved' => true,
                                ])
                                <button
                                    type="button"
                                    x-on:click="showNotes = !showNotes"
                                    class="ml-auto rounded-full p-1 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                                    title="Notes"
                                >
                                    <x-heroicon-o-chat-bubble-left class="h-4 w-4" />
                                </button>
                            </div>

                            {{-- Item Notes --}}
                            <div x-show="showNotes" x-collapse class="mt-2">
                                <div x-data="{ noteText: '{{ str_replace("'", "\\'", $item['notes'] ?? '') }}' }">
                                    <textarea
                                        x-model="noteText"
                                        rows="2"
                                        placeholder="Add a note..."
                                        class="block w-full rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    ></textarea>
                                    <button
                                        type="button"
                                        x-on:click="$wire.addItemNote('{{ $item['key'] }}', noteText)"
                                        class="mt-1 rounded bg-primary-600 px-2 py-1 text-xs font-medium text-white hover:bg-primary-700"
                                    >
                                        Save Note
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-filament::section>
    @endforeach

    {{-- Notes --}}
    <x-filament::section>
        <x-slot name="heading">Property Notes</x-slot>
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

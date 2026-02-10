<x-filament-panels::page>
    @if(count($this->properties) < 2)
        <x-filament::section>
            <div class="py-8 text-center">
                <x-heroicon-o-scale class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                <p class="mt-2 text-gray-500 dark:text-gray-400">Save at least 2 properties to compare them.</p>
                <a href="{{ url('/app') }}" class="mt-4 inline-block">
                    <x-filament::button>Back to Dashboard</x-filament::button>
                </a>
            </div>
        </x-filament::section>
    @else
        {{-- Controls Bar --}}
        <div class="mb-4 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="sortBy" class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                <select
                    id="sortBy"
                    wire:model.live="sortBy"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                >
                    <option value="score">Score</option>
                    <option value="name">Name</option>
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input
                    type="checkbox"
                    wire:model.live="filterDifferences"
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800"
                >
                Show differences only
            </label>
        </div>

        {{-- Comparison Table --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <table class="w-full text-sm">
                <thead>
                    {{-- Header: Property addresses + score badges --}}
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="min-w-[200px] px-4 py-3 text-left text-gray-500 dark:text-gray-400">Property</th>
                        @foreach($this->properties as $prop)
                            <th class="min-w-[160px] px-4 py-3 text-center">
                                <a href="{{ \App\Filament\Pages\PropertyDetailPage::getUrl(['property' => $prop['property']->id]) }}" class="text-primary-600 hover:underline dark:text-primary-400">
                                    {{ $prop['property']->address_line_1 }}
                                </a>
                                <p class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ $prop['property']->postcode }}</p>
                                <div class="mt-1">
                                    @php
                                        $pct = $prop['weightedScore']['percentage'];
                                        $badgeColor = match(true) {
                                            $pct >= 70 => 'success',
                                            $pct >= 40 => 'warning',
                                            default => 'danger',
                                        };
                                    @endphp
                                    <x-filament::badge :color="$badgeColor" size="sm">
                                        {{ $pct }}%
                                    </x-filament::badge>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{-- Category Groups --}}
                    @foreach($this->getComparisonData() as $category => $categoryData)
                        {{-- Category Header --}}
                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                            <td colspan="{{ count($this->properties) + 1 }}" class="px-4 py-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $categoryData['category_label'] }}</span>
                            </td>
                        </tr>

                        @foreach($categoryData['items'] as $item)
                            @php
                                $isDealBreaker = $item['severity'] === 'deal_breaker';
                                $hasDislike = collect($this->properties)->contains(fn ($p) => ($p['assessments'][$item['key']] ?? null) === 'dislike');
                            @endphp
                            <tr class="border-b border-gray-200 dark:border-gray-700 {{ $isDealBreaker && $hasDislike ? 'border-l-2 border-l-red-500' : '' }}">
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center gap-1.5">
                                        {{ $item['label'] }}
                                        @if($isDealBreaker)
                                            <span class="inline-block h-2 w-2 rounded-full bg-red-500" title="Deal-breaker"></span>
                                        @elseif($item['severity'] === 'important')
                                            <span class="inline-block h-2 w-2 rounded-full bg-amber-500" title="Important"></span>
                                        @else
                                            <span class="inline-block h-2 w-2 rounded-full bg-gray-400" title="Nice to have"></span>
                                        @endif
                                    </div>
                                </td>
                                @foreach($this->properties as $prop)
                                    @php $assessment = $prop['assessments'][$item['key']] ?? null; @endphp
                                    <td class="px-4 py-2 text-center {{ match($assessment) {
                                        'like' => 'bg-green-50 dark:bg-green-900/20',
                                        'neutral' => 'bg-amber-50 dark:bg-amber-900/20',
                                        'dislike' => 'bg-red-50 dark:bg-red-900/20',
                                        default => 'bg-gray-50 dark:bg-gray-800',
                                    } }}">
                                        @if($assessment === 'like')
                                            <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                                <x-heroicon-s-hand-thumb-up class="h-4 w-4" />
                                            </span>
                                        @elseif($assessment === 'dislike')
                                            <span class="inline-flex items-center text-red-600 dark:text-red-400">
                                                <x-heroicon-s-hand-thumb-down class="h-4 w-4" />
                                            </span>
                                        @elseif($assessment === 'neutral')
                                            <span class="inline-flex items-center text-amber-500 dark:text-amber-400">
                                                <x-heroicon-s-minus-circle class="h-4 w-4" />
                                            </span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach

                    {{-- Summary Section --}}
                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">Weighted Score</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                <div class="mx-auto max-w-[120px]">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $prop['weightedScore']['percentage'] }}%</span>
                                    <div class="mt-1 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                        @php
                                            $scorePct = $prop['weightedScore']['percentage'];
                                            $barColor = match(true) {
                                                $scorePct >= 70 => 'bg-green-500',
                                                $scorePct >= 40 => 'bg-amber-500',
                                                default => 'bg-red-500',
                                            };
                                        @endphp
                                        <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $scorePct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $prop['weightedScore']['score'] }}/{{ $prop['weightedScore']['max'] }}</span>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">Deal-breakers</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                @if($prop['progress']['dealBreakers'] > 0)
                                    <x-filament::badge color="danger">{{ $prop['progress']['dealBreakers'] }} flagged</x-filament::badge>
                                @else
                                    <x-filament::badge color="success">None</x-filament::badge>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">Progress</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $prop['progress']['assessed'] }}/{{ $prop['progress']['total'] }} assessed</span>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Recommendation --}}
        @if($this->getRecommendationText())
            <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <div class="flex items-start gap-2">
                    <x-heroicon-o-light-bulb class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" />
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ $this->getRecommendationText() }}</p>
                </div>
            </div>
        @endif
    @endif
</x-filament-panels::page>

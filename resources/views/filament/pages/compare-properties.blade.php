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
        {{-- Summary Row --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="min-w-[200px] px-4 py-3 text-left text-gray-500 dark:text-gray-400">Property</th>
                        @foreach($this->properties as $prop)
                            <th class="min-w-[180px] px-4 py-3 text-center">
                                <a href="{{ \App\Filament\Pages\PropertyDetailPage::getUrl(['property' => $prop['property']->id]) }}" class="text-primary-600 hover:underline">
                                    {{ $prop['property']->address_line_1 }}
                                </a>
                                <p class="text-xs font-normal text-gray-500">{{ $prop['property']->postcode }}</p>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{-- Progress Summary --}}
                    <tr class="border-b bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Progress</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                <div class="mx-auto max-w-[120px]">
                                    <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-1.5 rounded-full bg-primary-600" style="width: {{ $prop['progress']['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $prop['progress']['percentage'] }}%</span>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Score Row --}}
                    <tr class="border-b bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Score</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2 text-xs">
                                    <span class="text-green-600">{{ $prop['progress']['likes'] }} likes</span>
                                    <span class="text-red-600">{{ $prop['progress']['dislikes'] }} dislikes</span>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    {{-- Deal-breakers Row --}}
                    <tr class="border-b {{ collect($this->properties)->contains(fn ($p) => $p['progress']['dealBreakers'] > 0) ? 'bg-red-50 dark:bg-red-900/10' : 'bg-gray-50 dark:bg-gray-800/50' }} dark:border-gray-700">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Deal-breakers</td>
                        @foreach($this->properties as $prop)
                            <td class="px-4 py-3 text-center">
                                @if($prop['progress']['dealBreakers'] > 0)
                                    <x-filament::badge color="danger">{{ $prop['progress']['dealBreakers'] }}</x-filament::badge>
                                @else
                                    <x-filament::badge color="success">None</x-filament::badge>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Checklist Items by Category --}}
                    @php
                        $itemsByCategory = collect(config('housescout.checklist.items', []))->groupBy('category');
                    @endphp

                    @foreach($itemsByCategory as $category => $items)
                        {{-- Category Header --}}
                        <tr class="border-b dark:border-gray-700">
                            <td colspan="{{ count($this->properties) + 1 }}" class="px-4 py-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ Str::title($category) }}</span>
                            </td>
                        </tr>

                        @foreach($items as $item)
                            @php
                                $isDealBreaker = $item['is_deal_breaker'] ?? false;
                                $hasDislike = collect($this->properties)->contains(fn ($p) => ($p['assessments'][$item['key']] ?? null) === 'dislike');
                            @endphp
                            <tr class="border-b dark:border-gray-700 {{ $isDealBreaker && $hasDislike ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                    {{ $item['label'] }}
                                    @if($isDealBreaker)
                                        <span class="ml-1 text-xs text-red-500" title="Deal-breaker">!</span>
                                    @endif
                                </td>
                                @foreach($this->properties as $prop)
                                    @php $assessment = $prop['assessments'][$item['key']] ?? null; @endphp
                                    <td class="px-4 py-2 text-center">
                                        @if($assessment === 'like')
                                            <span class="inline-flex items-center text-green-600">
                                                <x-heroicon-s-hand-thumb-up class="h-4 w-4" />
                                            </span>
                                        @elseif($assessment === 'dislike')
                                            <span class="inline-flex items-center text-red-600">
                                                <x-heroicon-s-hand-thumb-down class="h-4 w-4" />
                                            </span>
                                        @elseif($assessment === 'neutral')
                                            <span class="inline-flex items-center text-yellow-500">
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
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>

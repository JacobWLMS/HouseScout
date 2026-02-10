<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between">
            <span>Planning</span>
            @include('filament.pages.partials.assessment-control', ['itemKey' => 'planning_issues', 'assessment' => $assessments['planning_issues'] ?? null, 'saved' => (bool) $this->savedProperty])
        </div>
    </x-slot>

    @if($this->property->planningApplications->isNotEmpty())
        @php
            $pending = $this->property->planningApplications->filter(fn($app) => in_array(strtolower($app->status ?? ''), ['pending', 'awaiting', 'under review']));
            $recent = $this->property->planningApplications->sortByDesc('application_date')->take(3);
        @endphp

        <div class="space-y-3">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->property->planningApplications->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">applications found</p>
                </div>
                @if($pending->count() > 0)
                    <x-filament::badge color="warning">{{ $pending->count() }} pending</x-filament::badge>
                @endif
            </div>

            @foreach($recent as $app)
                <div class="border-t border-gray-100 pt-2 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-900 dark:text-white">{{ Str::limit($app->description, 60) }}</p>
                    <div class="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $app->reference }}</span>
                        <x-filament::badge size="sm" :color="match(strtolower($app->status ?? '')) {
                            'approved', 'granted', 'permitted' => 'success',
                            'refused', 'rejected', 'declined' => 'danger',
                            'pending', 'awaiting' => 'warning',
                            default => 'gray',
                        }">
                            {{ $app->status ?? 'Unknown' }}
                        </x-filament::badge>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No planning applications found.</p>
    @endif
</x-filament::section>

<x-filament-panels::page>
    {{-- Badges --}}
    <div class="flex flex-wrap items-center gap-3">
        <x-filament::badge color="primary" size="lg">
            {{ $this->demandCount }} {{ Str::plural('person', $this->demandCount) }} searched in last 30 days
        </x-filament::badge>
        @if($this->property->property_type)
            <x-filament::badge color="gray">{{ $this->property->property_type }}</x-filament::badge>
        @endif
        @if($this->property->built_form)
            <x-filament::badge color="gray">{{ $this->property->built_form }}</x-filament::badge>
        @endif
        @if($this->property->floor_area)
            <x-filament::badge color="gray">{{ $this->property->floor_area }} m&sup2;</x-filament::badge>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Content (2/3 width) --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Map --}}
            @if($this->property->latitude && $this->property->longitude)
                @livewire('map-embed', ['property' => $this->property], key('map-' . $this->property->id))
            @else
                <x-filament::section>
                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                        <p>Map unavailable — property coordinates not found.</p>
                    </div>
                </x-filament::section>
            @endif

            {{-- Data Panels Grid --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @include('filament.pages.partials.panel-energy')
                @include('filament.pages.partials.panel-flood')
                @include('filament.pages.partials.panel-crime')
                @include('filament.pages.partials.panel-planning')
                @include('filament.pages.partials.panel-sales')
                @include('filament.pages.partials.panel-placeholder', ['title' => 'Schools', 'itemKey' => 'schools_nearby'])
            </div>
        </div>

        {{-- Sidebar (1/3 width) --}}
        <div>
            @if($this->savedProperty)
                @include('filament.pages.partials.checklist-sidebar')
            @else
                <x-filament::section>
                    <div class="space-y-4 py-4 text-center">
                        <x-heroicon-o-clipboard-document-check class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Start Your Assessment</p>
                            <p class="mt-1 text-sm text-gray-500">Save this property to begin your checklist and track what matters most.</p>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

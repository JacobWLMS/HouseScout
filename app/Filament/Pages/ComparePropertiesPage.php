<?php

namespace App\Filament\Pages;

use App\Models\SavedProperty;
use App\Services\ChecklistService;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ComparePropertiesPage extends Page
{
    protected static ?string $slug = 'compare';

    protected static ?string $title = 'Compare Properties';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.compare-properties';

    /** @var array<int, array<string, mixed>> */
    public array $properties = [];

    /** @var array<string, array<string, mixed>> */
    public array $checklistItems = [];

    /** @var array<string, string> */
    public array $categories = [];

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SavedProperty::where('user_id', Auth::id())->count() >= 2;
    }

    public function mount(): void
    {
        $checklistService = app(ChecklistService::class);
        $items = config('housescout.checklist.items', []);

        $this->checklistItems = collect($items)->keyBy('key')->toArray();
        $this->categories = collect($items)->pluck('category')->unique()->values()->toArray();

        $savedProperties = SavedProperty::query()
            ->where('user_id', Auth::id())
            ->with(['property', 'assessments'])
            ->latest()
            ->get();

        $this->properties = $savedProperties->map(function (SavedProperty $saved) use ($checklistService) {
            return [
                'saved' => $saved,
                'property' => $saved->property,
                'assessments' => $saved->assessments->pluck('assessment', 'item_key')->toArray(),
                'progress' => $checklistService->getProgress($saved),
            ];
        })->toArray();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '/' => 'Dashboard',
            '#' => 'Compare Properties',
        ];
    }
}

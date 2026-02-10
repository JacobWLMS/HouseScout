<?php

namespace App\Filament\Pages;

use App\Models\SavedProperty;
use App\Services\ChecklistService;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class ComparePropertiesPage extends Page
{
    protected static ?string $slug = 'compare';

    protected static ?string $title = 'Compare Properties';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.compare-properties';

    /** @var array<int, array<string, mixed>> */
    public array $properties = [];

    #[Url]
    public string $sortBy = 'score';

    #[Url]
    public bool $filterDifferences = false;

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
        $this->loadProperties();
    }

    public function updatedSortBy(): void
    {
        $this->loadProperties();
    }

    public function updatedFilterDifferences(): void
    {
        $this->loadProperties();
    }

    private function loadProperties(): void
    {
        $checklistService = app(ChecklistService::class);

        $savedProperties = SavedProperty::query()
            ->where('user_id', Auth::id())
            ->with(['property', 'assessments'])
            ->latest()
            ->limit(6)
            ->get();

        $this->properties = $savedProperties->map(function (SavedProperty $saved) use ($checklistService) {
            return [
                'saved' => $saved,
                'property' => $saved->property,
                'assessments' => $saved->assessments->pluck('assessment', 'item_key')->toArray(),
                'progress' => $checklistService->getProgress($saved),
                'weightedScore' => $checklistService->getWeightedScore($saved),
            ];
        })->toArray();

        if ($this->sortBy === 'score') {
            usort($this->properties, fn ($a, $b) => $b['weightedScore']['percentage'] <=> $a['weightedScore']['percentage']);
        } elseif ($this->sortBy === 'name') {
            usort($this->properties, fn ($a, $b) => ($a['property']->address_line_1 ?? '') <=> ($b['property']->address_line_1 ?? ''));
        }
    }

    /**
     * @return Collection<string, array{category_label: string, items: Collection}>
     */
    public function getComparisonData(): Collection
    {
        $checklistService = app(ChecklistService::class);

        $savedProperties = collect($this->properties)->map(fn ($p) => $p['saved']);

        if ($savedProperties->isEmpty()) {
            return collect();
        }

        $grouped = $checklistService->getGroupedChecklist($savedProperties->first());

        return $grouped->map(function (array $categoryData) {
            $items = $categoryData['items']->filter(function (array $item) {
                if (! $this->filterDifferences) {
                    return true;
                }

                $verdicts = collect($this->properties)
                    ->map(fn ($p) => $p['assessments'][$item['key']] ?? null)
                    ->unique()
                    ->values();

                return $verdicts->count() > 1;
            });

            return [
                'category_label' => $categoryData['category_label'],
                'items' => $items,
            ];
        })->filter(fn ($cat) => $cat['items']->isNotEmpty());
    }

    public function getRecommendationText(): string
    {
        if (count($this->properties) < 2) {
            return '';
        }

        $sorted = collect($this->properties)->sortByDesc('weightedScore.percentage');
        $best = $sorted->first();
        $bestAddress = $best['property']->address_line_1;
        $bestScore = $best['weightedScore']['percentage'];

        $text = "{$bestAddress} scores highest at {$bestScore}%.";

        $dealBreakerProperties = $sorted->filter(fn ($p) => $p['progress']['dealBreakers'] > 0);
        if ($dealBreakerProperties->isNotEmpty()) {
            $names = $dealBreakerProperties->map(fn ($p) => $p['property']->address_line_1)->join(', ', ' and ');
            $counts = $dealBreakerProperties->map(fn ($p) => $p['progress']['dealBreakers'])->sum();
            $text .= " Note: {$names} has {$counts} unresolved deal-breakers.";
        }

        return $text;
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

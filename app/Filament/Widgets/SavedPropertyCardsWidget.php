<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ComparePropertiesPage;
use App\Filament\Pages\PropertyDetailPage;
use App\Models\SavedProperty;
use App\Services\ChecklistService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SavedPropertyCardsWidget extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.saved-property-cards';

    /**
     * @return array<string, mixed>
     */
    public function getSavedProperties(): array
    {
        $checklistService = app(ChecklistService::class);

        $savedProperties = SavedProperty::query()
            ->where('user_id', Auth::id())
            ->with('property')
            ->latest()
            ->get();

        return $savedProperties->map(function (SavedProperty $saved) use ($checklistService) {
            $progress = $checklistService->getProgress($saved);

            return [
                'id' => $saved->id,
                'property' => $saved->property,
                'progress' => $progress,
                'url' => PropertyDetailPage::getUrl(['property' => $saved->property_id]),
            ];
        })->toArray();
    }

    public function getCompareUrl(): string
    {
        return ComparePropertiesPage::getUrl();
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Property;
use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class PropertyDetailPage extends Page
{
    protected static ?string $slug = 'properties/{property}';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Property Detail';

    protected string $view = 'filament.pages.property-detail';

    public Property $property;

    public ?SavedProperty $savedProperty = null;

    public string $notes = '';

    public int $demandCount = 0;

    /** @var array<string, string|null> */
    public array $assessments = [];

    /** @var array<string, mixed> */
    public array $checklistProgress = [];

    public function mount(Property $property): void
    {
        $this->property = $property->load([
            'epcData',
            'floodRiskData',
            'landRegistryData',
            'planningApplications',
            'crimeData',
        ]);

        $this->savedProperty = SavedProperty::query()
            ->where('user_id', Auth::id())
            ->where('property_id', $this->property->id)
            ->first();

        $this->notes = $this->savedProperty?->notes ?? '';

        $this->demandCount = $this->property->propertySearches()
            ->where('searched_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count('user_id');

        $this->loadChecklistData();
    }

    public function getHeading(): string
    {
        return $this->property->address_line_1.', '.$this->property->postcode;
    }

    public function getSubheading(): ?string
    {
        $parts = array_filter([
            $this->property->address_line_2,
            $this->property->city,
            $this->property->county,
        ]);

        return implode(', ', $parts) ?: null;
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleSave')
                ->label($this->savedProperty ? 'Unsave Property' : 'Save & Start Checklist')
                ->icon($this->savedProperty ? Heroicon::Bookmark : Heroicon::OutlinedBookmark)
                ->color($this->savedProperty ? 'danger' : 'primary')
                ->action(function (): void {
                    if ($this->savedProperty) {
                        try {
                            $this->savedProperty->assessments()->delete();
                        } catch (\Throwable) {
                            // PropertyAssessment table may not exist yet
                        }
                        $this->savedProperty->delete();
                        $this->savedProperty = null;
                        $this->notes = '';
                        $this->assessments = [];
                        $this->checklistProgress = [];

                        Notification::make()
                            ->title('Property removed from saved')
                            ->success()
                            ->send();
                    } else {
                        $this->savedProperty = SavedProperty::create([
                            'user_id' => Auth::id(),
                            'property_id' => $this->property->id,
                            'notes' => $this->notes ?: null,
                        ]);

                        try {
                            $checklistService = app(ChecklistService::class);
                            $checklistService->initializeChecklist($this->savedProperty);
                        } catch (\Throwable) {
                            // ChecklistService may not exist yet
                        }

                        $this->loadChecklistData();

                        Notification::make()
                            ->title('Property saved — checklist started!')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }

    public function saveNotes(): void
    {
        if (! $this->savedProperty) {
            Notification::make()
                ->title('Save the property first to add notes')
                ->warning()
                ->send();

            return;
        }

        $this->savedProperty->update(['notes' => $this->notes]);

        Notification::make()
            ->title('Notes saved')
            ->success()
            ->send();
    }

    public function loadChecklistData(): void
    {
        if ($this->savedProperty) {
            try {
                $this->assessments = $this->savedProperty->assessments()
                    ->pluck('assessment', 'item_key')
                    ->toArray();
            } catch (\Throwable) {
                $this->assessments = [];
            }

            try {
                $checklistService = app(ChecklistService::class);
                $this->checklistProgress = $checklistService->getProgress($this->savedProperty);
            } catch (\Throwable) {
                $this->checklistProgress = [];
            }
        } else {
            $this->assessments = [];
            $this->checklistProgress = [];
        }
    }

    public function assessItem(string $itemKey, string $assessment): void
    {
        if (! $this->savedProperty) {
            return;
        }

        PropertyAssessment::updateOrCreate(
            [
                'saved_property_id' => $this->savedProperty->id,
                'item_key' => $itemKey,
            ],
            [
                'assessment' => $assessment,
                'is_auto_assessed' => false,
            ]
        );

        $this->loadChecklistData();
    }

    public function removeAssessment(string $itemKey): void
    {
        if (! $this->savedProperty) {
            return;
        }

        PropertyAssessment::query()
            ->where('saved_property_id', $this->savedProperty->id)
            ->where('item_key', $itemKey)
            ->update(['assessment' => null]);

        $this->loadChecklistData();
    }

    /** @return array<int, array<string, mixed>> */
    public function getDealBreakerItems(): array
    {
        $items = config('housescout.checklist.items', []);

        return collect($items)
            ->filter(function ($item) {
                return ($item['is_deal_breaker'] ?? false)
                    && isset($this->assessments[$item['key']])
                    && $this->assessments[$item['key']] === 'dislike';
            })
            ->values()
            ->toArray();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '/' => 'Dashboard',
            '#' => $this->property->postcode,
        ];
    }
}

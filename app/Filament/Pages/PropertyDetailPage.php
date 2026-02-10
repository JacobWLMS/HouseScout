<?php

namespace App\Filament\Pages;

use App\Models\Property;
use App\Models\SavedProperty;
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
                ->label($this->savedProperty ? 'Unsave Property' : 'Save Property')
                ->icon($this->savedProperty ? Heroicon::Bookmark : Heroicon::OutlinedBookmark)
                ->color($this->savedProperty ? 'danger' : 'primary')
                ->action(function (): void {
                    if ($this->savedProperty) {
                        $this->savedProperty->delete();
                        $this->savedProperty = null;
                        $this->notes = '';

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

                        Notification::make()
                            ->title('Property saved')
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

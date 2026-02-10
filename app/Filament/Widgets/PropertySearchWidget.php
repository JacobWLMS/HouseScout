<?php

namespace App\Filament\Widgets;

use App\Exceptions\InvalidPostcodeException;
use App\Filament\Pages\PropertyDetailPage;
use App\Services\PropertySearchService;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class PropertySearchWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.property-search-widget';

    #[Validate('required|string|min:3|max:100')]
    public string $query = '';

    public function search(): void
    {
        $this->validate();

        try {
            $service = app(PropertySearchService::class);
            $property = $service->search(Auth::user(), $this->query);

            $this->query = '';

            $this->redirect(PropertyDetailPage::getUrl(['property' => $property->id]));
        } catch (InvalidPostcodeException $e) {
            Notification::make()
                ->title('Search Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Search Error')
                ->body('An unexpected error occurred. Please try again.')
                ->danger()
                ->send();
        }
    }
}

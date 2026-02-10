<?php

namespace App\Filament\Widgets;

use App\DataObjects\AddressResult;
use App\DataObjects\PostcodeLookupResult;
use App\Exceptions\EpcApiKeyMissingException;
use App\Filament\Pages\PropertyDetailPage;
use App\Services\AddressLookupService;
use App\Services\PostcodeService;
use App\Services\PropertySearchService;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PropertySearchWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.property-search-widget';

    public string $query = '';

    /** @var array<int, string> */
    public array $postcodeSuggestions = [];

    public ?string $selectedPostcode = null;

    /** @var array<int, array<string, mixed>> */
    public array $addresses = [];

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?string $adminDistrict = null;

    public ?string $adminCounty = null;

    public ?string $lsoa = null;

    public ?string $msoa = null;

    public ?string $ward = null;

    public ?string $constituency = null;

    public ?int $easting = null;

    public ?int $northing = null;

    public ?string $localAuthority = null;

    public bool $isLoadingAddresses = false;

    public bool $showDropdown = false;

    public function updatedQuery(): void
    {
        $this->selectedPostcode = null;
        $this->addresses = [];
        $this->latitude = null;
        $this->longitude = null;
        $this->adminDistrict = null;
        $this->adminCounty = null;
        $this->lsoa = null;
        $this->msoa = null;
        $this->ward = null;
        $this->constituency = null;
        $this->easting = null;
        $this->northing = null;
        $this->localAuthority = null;

        if (strlen(trim($this->query)) < 2) {
            $this->postcodeSuggestions = [];
            $this->showDropdown = false;

            return;
        }

        $service = app(PostcodeService::class);
        $this->postcodeSuggestions = $service->autocomplete($this->query);
        $this->showDropdown = ! empty($this->postcodeSuggestions);
    }

    public function selectPostcode(string $postcode): void
    {
        $this->selectedPostcode = $postcode;
        $this->query = $postcode;
        $this->postcodeSuggestions = [];
        $this->isLoadingAddresses = true;
        $this->showDropdown = true;

        $postcodeService = app(PostcodeService::class);
        $lookupResult = $postcodeService->lookup($postcode);

        if ($lookupResult) {
            $this->latitude = $lookupResult->latitude;
            $this->longitude = $lookupResult->longitude;
            $this->adminDistrict = $lookupResult->adminDistrict;
            $this->adminCounty = $lookupResult->adminCounty;
            $this->lsoa = $lookupResult->lsoa;
            $this->msoa = $lookupResult->msoa;
            $this->ward = $lookupResult->ward;
            $this->constituency = $lookupResult->constituency;
            $this->easting = $lookupResult->easting;
            $this->northing = $lookupResult->northing;
            $this->localAuthority = $lookupResult->localAuthority;
        }

        try {
            $addressService = app(AddressLookupService::class);
            $results = $addressService->searchByPostcode($postcode);
        } catch (EpcApiKeyMissingException $e) {
            $this->isLoadingAddresses = false;
            $this->showDropdown = false;
            Notification::make()
                ->title('Configuration Required')
                ->body('EPC API key is not configured. Please add EPC_API_KEY to your .env file.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $this->addresses = $results->map(fn (AddressResult $address) => $address->toArray())->all();
        $this->isLoadingAddresses = false;

        if (empty($this->addresses)) {
            Notification::make()
                ->title('No addresses found')
                ->body("No properties found at {$postcode}. This postcode may not have EPC records.")
                ->warning()
                ->send();
            $this->showDropdown = false;
        }
    }

    public function selectAddress(int $index): void
    {
        if (! isset($this->addresses[$index])) {
            return;
        }

        $addressData = AddressResult::fromArray($this->addresses[$index]);

        if (! $this->latitude || ! $this->longitude) {
            Notification::make()
                ->title('Search Error')
                ->body('Could not determine location data. Please try searching again.')
                ->danger()
                ->send();

            return;
        }

        $postcodeData = new PostcodeLookupResult(
            postcode: $this->selectedPostcode ?? $addressData->postcode,
            latitude: $this->latitude,
            longitude: $this->longitude,
            adminDistrict: $this->adminDistrict,
            adminCounty: $this->adminCounty,
            lsoa: $this->lsoa,
            msoa: $this->msoa,
            ward: $this->ward,
            constituency: $this->constituency,
            easting: $this->easting,
            northing: $this->northing,
            localAuthority: $this->localAuthority,
        );

        try {
            $service = app(PropertySearchService::class);
            $property = $service->selectProperty(Auth::user(), $addressData, $postcodeData);

            $this->resetSearch();

            $this->redirect(PropertyDetailPage::getUrl(['property' => $property->id]));
        } catch (\Exception $e) {
            report($e);
            Notification::make()
                ->title('Search Error')
                ->body('An unexpected error occurred. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function resetSearch(): void
    {
        $this->query = '';
        $this->postcodeSuggestions = [];
        $this->selectedPostcode = null;
        $this->addresses = [];
        $this->latitude = null;
        $this->longitude = null;
        $this->adminDistrict = null;
        $this->adminCounty = null;
        $this->lsoa = null;
        $this->msoa = null;
        $this->ward = null;
        $this->constituency = null;
        $this->easting = null;
        $this->northing = null;
        $this->localAuthority = null;
        $this->isLoadingAddresses = false;
        $this->showDropdown = false;
    }

    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }
}

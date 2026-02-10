<?php

namespace App\Services;

use App\DataObjects\AddressResult;
use App\DataObjects\PostcodeLookupResult;
use App\Exceptions\InvalidPostcodeException;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PropertySearchService
{
    public function __construct(
        private PostcodeService $postcodeService,
    ) {}

    /**
     * Select a specific property from address results, creating it if needed.
     * This is the main entry point for the new search flow.
     */
    public function selectProperty(User $user, AddressResult $address, PostcodeLookupResult $postcodeData): Property
    {
        // 1. Try to find existing property by UPRN
        $property = null;
        if ($address->uprn) {
            $property = Property::query()
                ->where('uprn', $address->uprn)
                ->first();
        }

        // 2. Fallback: find by address + postcode
        if (! $property) {
            $property = Property::query()
                ->where('address_line_1', $address->addressLine1)
                ->where('postcode', $address->postcode)
                ->first();
        }

        // 3. Create new property if not found
        if (! $property) {
            $property = Property::create([
                'address_line_1' => $address->addressLine1,
                'address_line_2' => $address->addressLine2,
                'city' => $postcodeData->adminDistrict ?? '',
                'county' => $postcodeData->adminCounty,
                'postcode' => $address->postcode,
                'uprn' => $address->uprn,
                'latitude' => $postcodeData->latitude,
                'longitude' => $postcodeData->longitude,
                'property_type' => $address->propertyType,
                'floor_area' => $address->floorArea,
                'lsoa' => $postcodeData->lsoa,
                'msoa' => $postcodeData->msoa,
                'ward' => $postcodeData->ward,
                'constituency' => $postcodeData->constituency,
                'easting' => $postcodeData->easting,
                'northing' => $postcodeData->northing,
                'local_authority' => $postcodeData->localAuthority,
            ]);
        } else {
            // Update existing property with any new data (e.g., lat/long if missing)
            $updates = [];
            if (! $property->latitude && $postcodeData->latitude) {
                $updates['latitude'] = $postcodeData->latitude;
            }
            if (! $property->longitude && $postcodeData->longitude) {
                $updates['longitude'] = $postcodeData->longitude;
            }
            if (! $property->uprn && $address->uprn) {
                $updates['uprn'] = $address->uprn;
            }
            if (! $property->lsoa && $postcodeData->lsoa) {
                $updates['lsoa'] = $postcodeData->lsoa;
            }
            if (! $property->msoa && $postcodeData->msoa) {
                $updates['msoa'] = $postcodeData->msoa;
            }
            if (! $property->ward && $postcodeData->ward) {
                $updates['ward'] = $postcodeData->ward;
            }
            if (! $property->constituency && $postcodeData->constituency) {
                $updates['constituency'] = $postcodeData->constituency;
            }
            if (! $property->easting && $postcodeData->easting) {
                $updates['easting'] = $postcodeData->easting;
            }
            if (! $property->northing && $postcodeData->northing) {
                $updates['northing'] = $postcodeData->northing;
            }
            if (! $property->local_authority && $postcodeData->localAuthority) {
                $updates['local_authority'] = $postcodeData->localAuthority;
            }
            if (! empty($updates)) {
                $property->update($updates);
            }
        }

        // 4. Create search audit record
        PropertySearch::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'searched_at' => now(),
            'search_query' => $address->fullAddress.', '.$address->postcode,
        ]);

        // 5. Dispatch data fetch job (non-blocking — don't fail property creation if queue is down)
        try {
            FetchPropertyDataJob::dispatch($property);
        } catch (\Exception $e) {
            Log::warning('Failed to dispatch FetchPropertyDataJob', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $property;
    }

    /**
     * Text-based search for existing properties in the database.
     * Used as a fallback when the user searches by text rather than postcode autocomplete.
     */
    public function search(User $user, string $query): Property
    {
        $query = trim($query);

        $property = Property::query()
            ->where('address_line_1', 'like', "%{$query}%")
            ->orWhere('postcode', 'like', "%{$query}%")
            ->orWhere('uprn', $query)
            ->first();

        if (! $property) {
            throw new InvalidPostcodeException("No property found for query: {$query}");
        }

        PropertySearch::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'searched_at' => now(),
            'search_query' => $query,
        ]);

        try {
            FetchPropertyDataJob::dispatch($property);
        } catch (\Exception $e) {
            Log::warning('Failed to dispatch FetchPropertyDataJob', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $property;
    }

    /**
     * @return Collection<int, PropertySearch>
     */
    public function getRecentSearches(User $user, int $limit = 10): Collection
    {
        return $user->propertySearches()
            ->with('property')
            ->latest('searched_at')
            ->limit($limit)
            ->get();
    }

    public function getDemandCount(Property $property, int $days = 30): int
    {
        return $property->propertySearches()
            ->where('searched_at', '>=', now()->subDays($days))
            ->distinct('user_id')
            ->count('user_id');
    }
}

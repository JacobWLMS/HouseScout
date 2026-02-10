<?php

namespace App\Services;

use App\Exceptions\InvalidPostcodeException;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use Illuminate\Support\Collection;

class PropertySearchService
{
    public function __construct(
        private PostcodeService $postcodeService,
    ) {}

    public function search(User $user, string $query): Property
    {
        $query = trim($query);

        if ($this->postcodeService->validate($query)) {
            $postcode = $this->postcodeService->normalize($query);
            $property = Property::query()
                ->where('postcode', $postcode)
                ->first();

            if (! $property) {
                $property = Property::create([
                    'address_line_1' => $postcode,
                    'city' => '',
                    'postcode' => $postcode,
                ]);
            }
        } else {
            $property = Property::query()
                ->where('address_line_1', 'like', "%{$query}%")
                ->orWhere('postcode', 'like', "%{$query}%")
                ->first();

            if (! $property) {
                throw new InvalidPostcodeException("No property found for query: {$query}");
            }
        }

        PropertySearch::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'searched_at' => now(),
            'search_query' => $query,
        ]);

        FetchPropertyDataJob::dispatch($property);

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

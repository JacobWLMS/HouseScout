<?php

namespace App\Services;

use App\DataObjects\AddressResult;
use App\Exceptions\EpcApiKeyMissingException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressLookupService
{
    /**
     * Search for all addresses at a given postcode using the EPC API.
     *
     * @return Collection<int, AddressResult>
     */
    public function searchByPostcode(string $postcode): Collection
    {
        $baseUrl = config('housescout.api.epc.base_url');
        $email = config('housescout.api.epc.email');
        $apiKey = config('housescout.api.epc.key');

        if (! $apiKey || ! $email) {
            throw new EpcApiKeyMissingException;
        }

        try {
            $response = Http::withBasicAuth($email, $apiKey)
                ->accept('application/json')
                ->get("{$baseUrl}/domestic/search", [
                    'postcode' => $postcode,
                    'size' => 5000,
                ]);

            if ($response->failed()) {
                Log::warning('EPC address lookup failed', [
                    'postcode' => $postcode,
                    'status' => $response->status(),
                ]);

                return collect();
            }

            $rows = $response->json('rows', []);

            return $this->deduplicateAndParse($rows, $postcode);
        } catch (\Exception $e) {
            Log::warning('EPC address lookup error', [
                'postcode' => $postcode,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Deduplicate by UPRN (keep most recent lodgement-date) and parse into AddressResult DTOs.
     *
     * @return Collection<int, AddressResult>
     */
    private function deduplicateAndParse(array $rows, string $postcode): Collection
    {
        // Group by UPRN, keep most recent per UPRN
        $grouped = collect($rows)
            ->filter(fn (array $row) => ! empty($row['uprn']))
            ->groupBy('uprn')
            ->map(function (Collection $group) {
                return $group->sortByDesc('lodgement-date')->first();
            });

        // Also include rows without UPRN (deduplicate by address)
        $withoutUprn = collect($rows)
            ->filter(fn (array $row) => empty($row['uprn']))
            ->unique('address');

        return $grouped->values()
            ->merge($withoutUprn)
            ->map(fn (array $row) => $this->parseRow($row, $postcode))
            ->sortBy('addressLine1')
            ->values();
    }

    private function parseRow(array $row, string $postcode): AddressResult
    {
        $address = $row['address'] ?? '';
        [$line1, $line2] = $this->parseAddress($address);

        return new AddressResult(
            fullAddress: $address,
            addressLine1: $line1,
            addressLine2: $line2,
            postcode: $postcode,
            uprn: $row['uprn'] ?? null,
            propertyType: $row['property-type'] ?? null,
            energyRating: $row['current-energy-rating'] ?? null,
            floorArea: isset($row['total-floor-area']) ? (float) $row['total-floor-area'] : null,
        );
    }

    /**
     * Parse EPC address format into line1 and line2.
     * EPC addresses are typically comma-separated: "10, DOWNING STREET, LONDON"
     * We take the first meaningful segment as line1, the rest as line2.
     *
     * @return array{0: string, 1: ?string}
     */
    private function parseAddress(string $address): array
    {
        $parts = array_map('trim', explode(',', $address));
        $parts = array_values(array_filter($parts, fn (string $part) => $part !== ''));

        if (count($parts) === 0) {
            return ['Unknown Address', null];
        }

        if (count($parts) === 1) {
            return [ucwords(strtolower($parts[0])), null];
        }

        // If first part is just a number, combine with second part
        if (is_numeric($parts[0]) && count($parts) > 1) {
            $line1 = ucwords(strtolower($parts[0].' '.$parts[1]));
            $remaining = array_slice($parts, 2);
        } else {
            $line1 = ucwords(strtolower($parts[0]));
            $remaining = array_slice($parts, 1);
        }

        $line2 = ! empty($remaining)
            ? ucwords(strtolower(implode(', ', $remaining)))
            : null;

        return [$line1, $line2];
    }
}

<?php

namespace App\Services;

use App\DataObjects\PostcodeLookupResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostcodeService
{
    private const UK_POSTCODE_PATTERN = '/^[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}$/i';

    public function validate(string $postcode): bool
    {
        return (bool) preg_match(self::UK_POSTCODE_PATTERN, trim($postcode));
    }

    public function normalize(string $postcode): string
    {
        $clean = strtoupper(preg_replace('/\s+/', '', $postcode));

        return substr($clean, 0, -3).' '.substr($clean, -3);
    }

    public function extractOutcode(string $postcode): string
    {
        $normalized = $this->normalize($postcode);

        return explode(' ', $normalized)[0];
    }

    public function extractIncode(string $postcode): string
    {
        $normalized = $this->normalize($postcode);

        return explode(' ', $normalized)[1];
    }

    public function autocomplete(string $partial): array
    {
        $partial = trim($partial);
        if (strlen($partial) < 2) {
            return [];
        }

        try {
            $baseUrl = config('housescout.api.postcodes.base_url', 'https://api.postcodes.io');
            $response = Http::get("{$baseUrl}/postcodes/{$partial}/autocomplete");

            if ($response->failed()) {
                return [];
            }

            return $response->json('result') ?? [];
        } catch (\Exception $e) {
            Log::warning('Postcode autocomplete failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    public function lookup(string $postcode): ?PostcodeLookupResult
    {
        $postcode = trim($postcode);

        try {
            $baseUrl = config('housescout.api.postcodes.base_url', 'https://api.postcodes.io');
            $response = Http::get("{$baseUrl}/postcodes/{$postcode}");

            if ($response->failed()) {
                return null;
            }

            $result = $response->json('result');
            if (! $result) {
                return null;
            }

            return new PostcodeLookupResult(
                postcode: $result['postcode'],
                latitude: (float) $result['latitude'],
                longitude: (float) $result['longitude'],
                adminDistrict: $result['admin_district'] ?? null,
                adminCounty: $result['admin_county'] ?? null,
                lsoa: $result['lsoa'] ?? null,
                msoa: $result['msoa'] ?? null,
                ward: $result['admin_ward'] ?? null,
                constituency: $result['parliamentary_constituency'] ?? null,
                easting: isset($result['eastings']) ? (int) $result['eastings'] : null,
                northing: isset($result['northings']) ? (int) $result['northings'] : null,
                localAuthority: $result['admin_district'] ?? null,
            );
        } catch (\Exception $e) {
            Log::warning('Postcode lookup failed', ['postcode' => $postcode, 'error' => $e->getMessage()]);

            return null;
        }
    }
}

<?php

namespace App\Services;

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
}

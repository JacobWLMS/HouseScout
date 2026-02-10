<?php

namespace App\DataObjects;

readonly class PostcodeLookupResult
{
    public function __construct(
        public string $postcode,
        public float $latitude,
        public float $longitude,
        public ?string $adminDistrict,
        public ?string $adminCounty,
        public ?string $lsoa = null,
        public ?string $msoa = null,
        public ?string $ward = null,
        public ?string $constituency = null,
        public ?int $easting = null,
        public ?int $northing = null,
        public ?string $localAuthority = null,
    ) {}

    public function toArray(): array
    {
        return [
            'postcode' => $this->postcode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'admin_district' => $this->adminDistrict,
            'admin_county' => $this->adminCounty,
            'lsoa' => $this->lsoa,
            'msoa' => $this->msoa,
            'ward' => $this->ward,
            'constituency' => $this->constituency,
            'easting' => $this->easting,
            'northing' => $this->northing,
            'local_authority' => $this->localAuthority,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            postcode: $data['postcode'],
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            adminDistrict: $data['admin_district'] ?? null,
            adminCounty: $data['admin_county'] ?? null,
            lsoa: $data['lsoa'] ?? null,
            msoa: $data['msoa'] ?? null,
            ward: $data['ward'] ?? null,
            constituency: $data['constituency'] ?? null,
            easting: isset($data['easting']) ? (int) $data['easting'] : null,
            northing: isset($data['northing']) ? (int) $data['northing'] : null,
            localAuthority: $data['local_authority'] ?? null,
        );
    }
}

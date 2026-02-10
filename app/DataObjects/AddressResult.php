<?php

namespace App\DataObjects;

readonly class AddressResult
{
    public function __construct(
        public string $fullAddress,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $postcode,
        public ?string $uprn,
        public ?string $propertyType,
        public ?string $energyRating,
        public ?float $floorArea,
    ) {}

    public function toArray(): array
    {
        return [
            'full_address' => $this->fullAddress,
            'address_line_1' => $this->addressLine1,
            'address_line_2' => $this->addressLine2,
            'postcode' => $this->postcode,
            'uprn' => $this->uprn,
            'property_type' => $this->propertyType,
            'energy_rating' => $this->energyRating,
            'floor_area' => $this->floorArea,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            fullAddress: $data['full_address'],
            addressLine1: $data['address_line_1'],
            addressLine2: $data['address_line_2'] ?? null,
            postcode: $data['postcode'],
            uprn: $data['uprn'] ?? null,
            propertyType: $data['property_type'] ?? null,
            energyRating: $data['energy_rating'] ?? null,
            floorArea: isset($data['floor_area']) ? (float) $data['floor_area'] : null,
        );
    }
}

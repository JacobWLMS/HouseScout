<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $postcodes = [
            'SW1A 1AA', 'EC2R 8AH', 'W1D 3SE', 'SE1 7PB', 'N1 9GU',
            'E14 5HP', 'WC2N 5DU', 'NW1 4SA', 'SW3 1AA', 'EC1A 1BB',
            'M1 1AE', 'B1 1BB', 'LS1 1UR', 'BS1 1EH', 'CF10 1EP',
            'EH1 1YZ', 'G1 1XQ', 'BN1 1AE', 'OX1 1BX', 'CB2 1TN',
        ];

        $counties = [
            'Greater London', 'West Midlands', 'Greater Manchester', 'West Yorkshire',
            'Kent', 'Essex', 'Hampshire', 'Surrey', 'Lancashire', 'Devon',
            'Hertfordshire', 'Norfolk', 'Suffolk', 'Oxfordshire', 'Somerset',
        ];

        $propertyTypes = ['Detached', 'Semi-Detached', 'Terraced', 'Flat', 'Maisonette', 'Bungalow', 'End-Terrace'];
        $builtForms = ['Detached', 'Semi-Detached', 'Mid-Terrace', 'End-Terrace', 'Enclosed Mid-Terrace', 'Enclosed End-Terrace'];

        return [
            'address_line_1' => fake()->buildingNumber().' '.fake()->streetName(),
            'address_line_2' => fake()->optional(0.3)->secondaryAddress(),
            'city' => fake()->city(),
            'county' => fake()->optional(0.7)->randomElement($counties),
            'postcode' => fake()->randomElement($postcodes),
            'uprn' => fake()->boolean(80) ? fake()->unique()->numerify('############') : null,
            'latitude' => fake()->latitude(50.0, 55.8),
            'longitude' => fake()->longitude(-5.7, 1.8),
            'property_type' => fake()->randomElement($propertyTypes),
            'built_form' => fake()->randomElement($builtForms),
            'floor_area' => fake()->optional(0.7)->randomFloat(2, 30, 500),
            'metadata' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\LandRegistryData;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandRegistryData>
 */
class LandRegistryDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenures = ['Freehold', 'Leasehold'];
        $lastSoldPrice = fake()->numberBetween(80000, 1500000);

        return [
            'property_id' => Property::factory(),
            'title_number' => strtoupper(fake()->bothify('??######')),
            'tenure' => fake()->randomElement($tenures),
            'last_sold_date' => fake()->dateTimeBetween('-20 years'),
            'last_sold_price' => $lastSoldPrice,
            'price_history' => [
                [
                    'date' => fake()->dateTimeBetween('-20 years', '-10 years')->format('Y-m-d'),
                    'price' => (int) ($lastSoldPrice * 0.6),
                ],
                [
                    'date' => fake()->dateTimeBetween('-10 years')->format('Y-m-d'),
                    'price' => $lastSoldPrice,
                ],
            ],
            'raw_response' => null,
            'fetched_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}

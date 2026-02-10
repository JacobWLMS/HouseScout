<?php

namespace Database\Factories;

use App\Models\CrimeData;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrimeData>
 */
class CrimeDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'anti-social-behaviour',
            'burglary',
            'criminal-damage-arson',
            'drugs',
            'other-theft',
            'possession-of-weapons',
            'public-order',
            'robbery',
            'shoplifting',
            'theft-from-the-person',
            'vehicle-crime',
            'violent-crime',
        ];

        return [
            'property_id' => Property::factory(),
            'month' => fake()->dateTimeBetween('-12 months')->format('Y-m'),
            'category' => fake()->randomElement($categories),
            'count' => fake()->numberBetween(0, 25),
        ];
    }
}

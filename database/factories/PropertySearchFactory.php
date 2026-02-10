<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertySearch>
 */
class PropertySearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $queries = [
            'SW1A 1AA', '3 bedroom house London', 'flat near station',
            'EC2R 8AH', 'semi-detached Manchester', 'bungalow Bristol',
        ];

        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'searched_at' => fake()->dateTimeBetween('-6 months'),
            'search_query' => fake()->randomElement($queries),
        ];
    }
}

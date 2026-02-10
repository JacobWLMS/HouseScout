<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedProperty>
 */
class SavedPropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'notes' => fake()->optional(0.5)->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyAssessment>
 */
class PropertyAssessmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'saved_property_id' => SavedProperty::factory(),
            'item_key' => fake()->randomElement(['epc_rating', 'flood_risk', 'crime_level', 'planning_issues', 'price_history']),
            'assessment' => fake()->optional(0.7)->randomElement(['like', 'dislike', 'neutral']),
            'is_auto_assessed' => fake()->boolean(30),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function like(): static
    {
        return $this->state(fn () => ['assessment' => 'like']);
    }

    public function dislike(): static
    {
        return $this->state(fn () => ['assessment' => 'dislike']);
    }

    public function neutral(): static
    {
        return $this->state(fn () => ['assessment' => 'neutral']);
    }

    public function autoAssessed(): static
    {
        return $this->state(fn () => ['is_auto_assessed' => true]);
    }
}

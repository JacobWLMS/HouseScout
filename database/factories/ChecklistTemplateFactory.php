<?php

namespace Database\Factories;

use App\Models\ChecklistTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChecklistTemplate>
 */
class ChecklistTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'flood_environmental' => 'Flood & Environmental',
            'price_value' => 'Price & Value',
            'energy_condition' => 'Energy & Condition',
            'legal_title' => 'Legal & Title',
            'planning_building' => 'Planning & Building',
            'crime' => 'Crime',
            'schools' => 'Schools',
            'connectivity' => 'Connectivity',
            'neighbourhood' => 'Neighbourhood',
        ];

        $category = fake()->randomElement(array_keys($categories));

        return [
            'category' => $category,
            'category_label' => $categories[$category],
            'key' => fake()->unique()->slug(2),
            'label' => fake()->words(3, true),
            'severity' => ChecklistTemplate::IMPORTANT,
            'type' => ChecklistTemplate::MANUAL,
            'guidance' => null,
            'link' => null,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function dealBreaker(): static
    {
        return $this->state(fn () => ['severity' => ChecklistTemplate::DEAL_BREAKER]);
    }

    public function important(): static
    {
        return $this->state(fn () => ['severity' => ChecklistTemplate::IMPORTANT]);
    }

    public function niceToHave(): static
    {
        return $this->state(fn () => ['severity' => ChecklistTemplate::NICE_TO_HAVE]);
    }

    public function automated(): static
    {
        return $this->state(fn () => ['type' => ChecklistTemplate::AUTOMATED]);
    }

    public function manual(): static
    {
        return $this->state(fn () => ['type' => ChecklistTemplate::MANUAL]);
    }

    public function withGuidance(): static
    {
        return $this->state(fn () => ['guidance' => fake()->sentence()]);
    }

    public function withLink(): static
    {
        return $this->state(fn () => ['link' => fake()->url()]);
    }
}

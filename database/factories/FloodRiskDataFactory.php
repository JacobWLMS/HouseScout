<?php

namespace Database\Factories;

use App\Models\FloodRiskData;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FloodRiskData>
 */
class FloodRiskDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $riskLevels = ['Very Low', 'Low', 'Medium', 'High'];
        $floodZones = ['Flood Zone 1', 'Flood Zone 2', 'Flood Zone 3a', 'Flood Zone 3b'];

        return [
            'property_id' => Property::factory(),
            'flood_risk_level' => fake()->randomElement($riskLevels),
            'flood_zone' => fake()->randomElement($floodZones),
            'river_and_sea_risk' => fake()->randomElement($riskLevels),
            'surface_water_risk' => fake()->randomElement($riskLevels),
            'reservoir_risk' => fake()->randomElement(['Risk of flooding', 'No risk identified']),
            'active_warnings' => [],
            'raw_response' => null,
            'fetched_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\EpcData;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EpcData>
 */
class EpcDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ratings = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $heatingDescriptions = [
            'Boiler and radiators, mains gas',
            'Boiler and radiators, oil',
            'Electric storage heaters',
            'Heat pump, radiators',
            'Warm air, mains gas',
            'Community scheme',
        ];
        $fuelTypes = ['Mains Gas', 'Electricity', 'Oil', 'LPG', 'Solid Fuel', 'Biomass'];

        $currentRatingIndex = fake()->numberBetween(0, 6);
        $potentialRatingIndex = fake()->numberBetween(0, $currentRatingIndex);

        $currentEfficiency = fake()->numberBetween(1, 100);
        $potentialEfficiency = fake()->numberBetween($currentEfficiency, 100);

        return [
            'property_id' => Property::factory(),
            'current_energy_rating' => $ratings[$currentRatingIndex],
            'potential_energy_rating' => $ratings[$potentialRatingIndex],
            'current_energy_efficiency' => $currentEfficiency,
            'potential_energy_efficiency' => $potentialEfficiency,
            'environment_impact_current' => fake()->numberBetween(1, 100),
            'environment_impact_potential' => fake()->numberBetween(1, 100),
            'energy_consumption_current' => fake()->numberBetween(50, 500),
            'energy_consumption_potential' => fake()->numberBetween(30, 300),
            'co2_emissions_current' => fake()->randomFloat(2, 0.5, 15.0),
            'co2_emissions_potential' => fake()->randomFloat(2, 0.3, 10.0),
            'lighting_cost_current' => fake()->numberBetween(30, 200),
            'lighting_cost_potential' => fake()->numberBetween(20, 150),
            'heating_cost_current' => fake()->numberBetween(200, 2000),
            'heating_cost_potential' => fake()->numberBetween(150, 1500),
            'hot_water_cost_current' => fake()->numberBetween(50, 500),
            'hot_water_cost_potential' => fake()->numberBetween(30, 400),
            'main_heating_description' => fake()->randomElement($heatingDescriptions),
            'main_fuel_type' => fake()->randomElement($fuelTypes),
            'lodgement_date' => fake()->dateTimeBetween('-5 years'),
            'raw_response' => null,
            'fetched_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}

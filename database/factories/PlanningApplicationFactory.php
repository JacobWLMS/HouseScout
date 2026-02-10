<?php

namespace Database\Factories;

use App\Models\PlanningApplication;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanningApplication>
 */
class PlanningApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Approved', 'Refused', 'Pending', 'Withdrawn', 'Appealed'];
        $types = [
            'Full Planning Permission',
            'Householder Planning Permission',
            'Outline Planning Permission',
            'Listed Building Consent',
            'Certificate of Lawful Development',
            'Prior Approval',
        ];
        $descriptions = [
            'Erection of single storey rear extension',
            'Conversion of loft space including rear dormer',
            'Change of use from office to residential',
            'Construction of two-storey side extension',
            'Demolition and rebuild of detached garage',
            'Installation of solar panels on roof',
            'New vehicular crossover and dropped kerb',
        ];

        $applicationDate = fake()->dateTimeBetween('-3 years');
        $status = fake()->randomElement($statuses);

        return [
            'property_id' => Property::factory(),
            'reference' => strtoupper(fake()->bothify('??/####/####')),
            'description' => fake()->randomElement($descriptions),
            'status' => $status,
            'decision_date' => $status !== 'Pending' ? fake()->dateTimeBetween($applicationDate) : null,
            'application_date' => $applicationDate,
            'application_type' => fake()->randomElement($types),
            'raw_response' => null,
            'fetched_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}

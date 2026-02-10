<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property string|null $current_energy_rating
 * @property string|null $potential_energy_rating
 * @property int|null $current_energy_efficiency
 * @property int|null $potential_energy_efficiency
 * @property int|null $environment_impact_current
 * @property int|null $environment_impact_potential
 * @property int|null $energy_consumption_current
 * @property int|null $energy_consumption_potential
 * @property string|null $co2_emissions_current
 * @property string|null $co2_emissions_potential
 * @property int|null $lighting_cost_current
 * @property int|null $lighting_cost_potential
 * @property int|null $heating_cost_current
 * @property int|null $heating_cost_potential
 * @property int|null $hot_water_cost_current
 * @property int|null $hot_water_cost_potential
 * @property string|null $main_heating_description
 * @property string|null $main_fuel_type
 * @property \Illuminate\Support\Carbon|null $lodgement_date
 * @property array|null $raw_response
 * @property \Illuminate\Support\Carbon|null $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EpcData extends Model
{
    /** @use HasFactory<\Database\Factories\EpcDataFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'current_energy_rating',
        'potential_energy_rating',
        'current_energy_efficiency',
        'potential_energy_efficiency',
        'environment_impact_current',
        'environment_impact_potential',
        'energy_consumption_current',
        'energy_consumption_potential',
        'co2_emissions_current',
        'co2_emissions_potential',
        'lighting_cost_current',
        'lighting_cost_potential',
        'heating_cost_current',
        'heating_cost_potential',
        'hot_water_cost_current',
        'hot_water_cost_potential',
        'main_heating_description',
        'main_fuel_type',
        'lodgement_date',
        'raw_response',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lodgement_date' => 'date',
            'co2_emissions_current' => 'decimal:2',
            'co2_emissions_potential' => 'decimal:2',
            'raw_response' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

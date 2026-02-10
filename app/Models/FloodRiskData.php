<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property string|null $flood_risk_level
 * @property string|null $flood_zone
 * @property string|null $river_and_sea_risk
 * @property string|null $surface_water_risk
 * @property string|null $reservoir_risk
 * @property array|null $active_warnings
 * @property array|null $raw_response
 * @property \Illuminate\Support\Carbon|null $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class FloodRiskData extends Model
{
    /** @use HasFactory<\Database\Factories\FloodRiskDataFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'flood_risk_level',
        'flood_zone',
        'river_and_sea_risk',
        'surface_water_risk',
        'reservoir_risk',
        'active_warnings',
        'raw_response',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active_warnings' => 'array',
            'raw_response' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

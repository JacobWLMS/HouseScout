<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $city
 * @property string|null $county
 * @property string $postcode
 * @property string|null $uprn
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $property_type
 * @property string|null $built_form
 * @property string|null $floor_area
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'uprn',
        'latitude',
        'longitude',
        'property_type',
        'built_form',
        'floor_area',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'floor_area' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function propertySearches(): HasMany
    {
        return $this->hasMany(PropertySearch::class);
    }

    public function epcData(): HasOne
    {
        return $this->hasOne(EpcData::class);
    }

    public function planningApplications(): HasMany
    {
        return $this->hasMany(PlanningApplication::class);
    }

    public function floodRiskData(): HasOne
    {
        return $this->hasOne(FloodRiskData::class);
    }

    public function crimeData(): HasMany
    {
        return $this->hasMany(CrimeData::class);
    }

    public function landRegistryData(): HasOne
    {
        return $this->hasOne(LandRegistryData::class);
    }

    public function savedProperties(): HasMany
    {
        return $this->hasMany(SavedProperty::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

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
 * @property string|null $lsoa
 * @property string|null $msoa
 * @property string|null $ward
 * @property string|null $constituency
 * @property int|null $easting
 * @property int|null $northing
 * @property string|null $local_authority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory, Searchable;

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'county' => $this->county,
            'uprn' => $this->uprn,
        ];
    }

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
        'lsoa',
        'msoa',
        'ward',
        'constituency',
        'easting',
        'northing',
        'local_authority',
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
            'easting' => 'integer',
            'northing' => 'integer',
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

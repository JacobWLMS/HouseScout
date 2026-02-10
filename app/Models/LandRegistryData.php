<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property string|null $title_number
 * @property string|null $tenure
 * @property \Illuminate\Support\Carbon|null $last_sold_date
 * @property int|null $last_sold_price
 * @property array|null $price_history
 * @property array|null $raw_response
 * @property \Illuminate\Support\Carbon|null $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class LandRegistryData extends Model
{
    /** @use HasFactory<\Database\Factories\LandRegistryDataFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'title_number',
        'tenure',
        'last_sold_date',
        'last_sold_price',
        'price_history',
        'raw_response',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_sold_date' => 'date',
            'price_history' => 'array',
            'raw_response' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

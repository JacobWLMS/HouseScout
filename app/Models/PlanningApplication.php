<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property string $reference
 * @property string|null $description
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $decision_date
 * @property \Illuminate\Support\Carbon|null $application_date
 * @property string|null $application_type
 * @property array|null $raw_response
 * @property \Illuminate\Support\Carbon|null $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PlanningApplication extends Model
{
    /** @use HasFactory<\Database\Factories\PlanningApplicationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'reference',
        'description',
        'status',
        'decision_date',
        'application_date',
        'application_type',
        'raw_response',
        'fetched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'decision_date' => 'date',
            'application_date' => 'date',
            'raw_response' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

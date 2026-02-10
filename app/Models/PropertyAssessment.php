<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $saved_property_id
 * @property string $item_key
 * @property string|null $assessment
 * @property bool $is_auto_assessed
 * @property array|null $auto_data
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PropertyAssessment extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyAssessmentFactory> */
    use HasFactory;

    protected $fillable = [
        'saved_property_id',
        'item_key',
        'assessment',
        'is_auto_assessed',
        'auto_data',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_auto_assessed' => 'boolean',
            'auto_data' => 'array',
        ];
    }

    public function savedProperty(): BelongsTo
    {
        return $this->belongsTo(SavedProperty::class);
    }
}

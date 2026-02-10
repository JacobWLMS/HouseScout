<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $category
 * @property string $category_label
 * @property string $key
 * @property string $label
 * @property string $severity
 * @property string $type
 * @property string|null $guidance
 * @property string|null $link
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ChecklistTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistTemplateFactory> */
    use HasFactory;

    public const string DEAL_BREAKER = 'deal_breaker';

    public const string IMPORTANT = 'important';

    public const string NICE_TO_HAVE = 'nice_to_have';

    public const string AUTOMATED = 'automated';

    public const string MANUAL = 'manual';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'category_label',
        'key',
        'label',
        'severity',
        'type',
        'guidance',
        'link',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<ChecklistTemplate>  $query
     * @return Builder<ChecklistTemplate>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<ChecklistTemplate>  $query
     * @return Builder<ChecklistTemplate>
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * @param  Builder<ChecklistTemplate>  $query
     * @return Builder<ChecklistTemplate>
     */
    public function scopeDealBreakers(Builder $query): Builder
    {
        return $query->where('severity', self::DEAL_BREAKER);
    }
}

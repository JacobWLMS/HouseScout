<?php

namespace App\Services;

use App\Models\ChecklistTemplate;
use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use Illuminate\Support\Collection;

class ChecklistService
{
    /**
     * Initialize checklist for a saved property — creates assessment records from DB templates and runs auto-assessment.
     */
    public function initializeChecklist(SavedProperty $saved): void
    {
        $templates = ChecklistTemplate::active()->orderBy('sort_order')->get();

        foreach ($templates as $template) {
            PropertyAssessment::firstOrCreate(
                [
                    'saved_property_id' => $saved->id,
                    'item_key' => $template->key,
                ],
                [
                    'assessment' => null,
                    'is_auto_assessed' => false,
                ]
            );
        }

        $this->autoAssess($saved);
    }

    /**
     * Run auto-assessment based on API data. Only sets assessments where none exists yet.
     */
    public function autoAssess(SavedProperty $saved): void
    {
        $property = $saved->property()->with([
            'epcData',
            'floodRiskData',
            'crimeData',
            'planningApplications',
            'landRegistryData',
        ])->first();

        if (! $property) {
            return;
        }

        $autoRules = $this->getAutoRules($property);

        foreach ($autoRules as $itemKey => $ruleResult) {
            if ($ruleResult['assessment'] === null) {
                continue;
            }

            PropertyAssessment::query()
                ->where('saved_property_id', $saved->id)
                ->where('item_key', $itemKey)
                ->whereNull('assessment')
                ->update([
                    'assessment' => $ruleResult['assessment'],
                    'is_auto_assessed' => true,
                    'auto_data' => json_encode($ruleResult['auto_data'] ?? []),
                ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getProgress(SavedProperty $saved): array
    {
        $templates = ChecklistTemplate::active()->orderBy('sort_order')->get();
        $assessments = $saved->assessments()->pluck('assessment', 'item_key');

        $total = $templates->count();
        $assessed = $assessments->filter(fn ($a) => $a !== null)->count();
        $likes = $assessments->filter(fn ($a) => $a === 'like')->count();
        $dislikes = $assessments->filter(fn ($a) => $a === 'dislike')->count();
        $neutral = $assessments->filter(fn ($a) => $a === 'neutral')->count();

        $dealBreakerKeys = $templates
            ->where('severity', ChecklistTemplate::DEAL_BREAKER)
            ->pluck('key');

        $dealBreakers = $dealBreakerKeys->filter(fn ($key) => ($assessments[$key] ?? null) === 'dislike')->count();

        $dealBreakerTotal = $dealBreakerKeys->count();
        $dealBreakerAssessed = $dealBreakerKeys->filter(fn ($key) => ($assessments[$key] ?? null) !== null)->count();

        $importantKeys = $templates->where('severity', ChecklistTemplate::IMPORTANT)->pluck('key');
        $importantTotal = $importantKeys->count();
        $importantAssessed = $importantKeys->filter(fn ($key) => ($assessments[$key] ?? null) !== null)->count();

        $niceToHaveKeys = $templates->where('severity', ChecklistTemplate::NICE_TO_HAVE)->pluck('key');
        $niceToHaveTotal = $niceToHaveKeys->count();
        $niceToHaveAssessed = $niceToHaveKeys->filter(fn ($key) => ($assessments[$key] ?? null) !== null)->count();

        return [
            'total' => $total,
            'assessed' => $assessed,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'neutral' => $neutral,
            'dealBreakers' => $dealBreakers,
            'percentage' => $total > 0 ? (int) round(($assessed / $total) * 100) : 0,
            'deal_breaker_total' => $dealBreakerTotal,
            'deal_breaker_assessed' => $dealBreakerAssessed,
            'important_total' => $importantTotal,
            'important_assessed' => $importantAssessed,
            'nice_to_have_total' => $niceToHaveTotal,
            'nice_to_have_assessed' => $niceToHaveAssessed,
        ];
    }

    /**
     * Calculate weighted score for a saved property.
     *
     * Weights: deal_breaker = 3, important = 2, nice_to_have = 1
     * Scoring: like = full, neutral = half, dislike = 0, unassessed = 0
     *
     * @return array{score: float, max: float, percentage: float}
     */
    public function getWeightedScore(SavedProperty $saved): array
    {
        $templates = ChecklistTemplate::active()->orderBy('sort_order')->get();
        $assessments = $saved->assessments()->pluck('assessment', 'item_key');

        $weights = [
            ChecklistTemplate::DEAL_BREAKER => 3,
            ChecklistTemplate::IMPORTANT => 2,
            ChecklistTemplate::NICE_TO_HAVE => 1,
        ];

        $score = 0.0;
        $max = 0.0;

        foreach ($templates as $template) {
            $weight = $weights[$template->severity] ?? 1;
            $max += $weight;

            $assessment = $assessments[$template->key] ?? null;

            $score += match ($assessment) {
                'like' => $weight,
                'neutral' => $weight / 2,
                default => 0,
            };
        }

        return [
            'score' => $score,
            'max' => $max,
            'percentage' => $max > 0 ? round(($score / $max) * 100, 1) : 0.0,
        ];
    }

    /**
     * Return templates grouped by category with assessments merged.
     *
     * @return Collection<string, array{category_label: string, items: Collection}>
     */
    public function getGroupedChecklist(SavedProperty $saved): Collection
    {
        $templates = ChecklistTemplate::active()->orderBy('sort_order')->get();
        $assessments = $saved->assessments()->get()->keyBy('item_key');

        return $templates->groupBy('category')->map(function (Collection $items, string $category) use ($assessments) {
            $categoryLabel = $items->first()->category_label;

            $mergedItems = $items->map(function (ChecklistTemplate $template) use ($assessments) {
                $assessment = $assessments[$template->key] ?? null;

                return [
                    'key' => $template->key,
                    'label' => $template->label,
                    'severity' => $template->severity,
                    'type' => $template->type,
                    'guidance' => $template->guidance,
                    'link' => $template->link,
                    'assessment' => $assessment?->assessment,
                    'is_auto_assessed' => $assessment?->is_auto_assessed ?? false,
                    'auto_data' => $assessment?->auto_data ?? [],
                    'notes' => $assessment?->notes,
                ];
            });

            return [
                'category_label' => $categoryLabel,
                'items' => $mergedItems,
            ];
        });
    }

    /**
     * @return array<string, array{assessment: string|null, auto_data: array<string, mixed>}>
     */
    private function getAutoRules($property): array
    {
        $rules = [];

        $this->assessEpcRules($property, $rules);
        $this->assessFloodRules($property, $rules);
        $this->assessCrimeRules($property, $rules);
        $this->assessPlanningRules($property, $rules);
        $this->assessLandRegistryRules($property, $rules);

        return $rules;
    }

    private function assessEpcRules($property, array &$rules): void
    {
        if (! $property->epcData || ! $property->epcData->fetched_at) {
            return;
        }

        $epc = $property->epcData;

        // EPC Rating: A/B/C -> like, D -> neutral, E/F/G -> dislike
        $rating = strtoupper($epc->current_energy_rating ?? '');
        $rules['epc_rating'] = [
            'assessment' => match (true) {
                in_array($rating, ['A', 'B', 'C']) => 'like',
                $rating === 'D' => 'neutral',
                in_array($rating, ['E', 'F', 'G']) => 'dislike',
                default => null,
            },
            'auto_data' => ['source' => 'epc', 'current_energy_rating' => $rating],
        ];

        // Heating System: heat pump -> like, gas boiler -> neutral, electric/oil -> dislike
        $heating = strtolower($epc->main_heating_description ?? '');
        if ($heating !== '') {
            $rules['heating_system'] = [
                'assessment' => match (true) {
                    str_contains($heating, 'heat pump') => 'like',
                    str_contains($heating, 'gas') => 'neutral',
                    str_contains($heating, 'electric') || str_contains($heating, 'oil') => 'dislike',
                    default => null,
                },
                'auto_data' => ['source' => 'epc', 'main_heating_description' => $epc->main_heating_description],
            ];
        }

        // Wall/Roof/Window Efficiency: based on current vs potential efficiency gap
        $current = (int) ($epc->current_energy_efficiency ?? 0);
        $potential = (int) ($epc->potential_energy_efficiency ?? 0);
        if ($current > 0 && $potential > 0) {
            $gap = $potential - $current;
            $rules['wall_roof_window_efficiency'] = [
                'assessment' => match (true) {
                    $gap <= 10 => 'like',
                    $gap <= 30 => 'neutral',
                    default => 'dislike',
                },
                'auto_data' => [
                    'source' => 'epc',
                    'current_energy_efficiency' => $current,
                    'potential_energy_efficiency' => $potential,
                    'gap' => $gap,
                ],
            ];
        }

        // Recommended Improvements: compare current vs potential EPC rating gap
        $potentialRating = strtoupper($epc->potential_energy_rating ?? '');
        if ($rating !== '' && $potentialRating !== '') {
            $ratingOrder = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7];
            $currentPos = $ratingOrder[$rating] ?? 7;
            $potentialPos = $ratingOrder[$potentialRating] ?? 7;
            $ratingGap = $currentPos - $potentialPos;

            $rules['recommended_improvements'] = [
                'assessment' => match (true) {
                    $ratingGap <= 0 => 'like',
                    $ratingGap === 1 => 'neutral',
                    default => 'dislike',
                },
                'auto_data' => [
                    'source' => 'epc',
                    'current_energy_rating' => $rating,
                    'potential_energy_rating' => $potentialRating,
                    'rating_gap' => $ratingGap,
                ],
            ];
        }
    }

    private function assessFloodRules($property, array &$rules): void
    {
        if (! $property->floodRiskData || ! $property->floodRiskData->fetched_at) {
            return;
        }

        $flood = $property->floodRiskData;

        // Flood Zone: Zone 1 -> like, Zone 2 -> neutral, Zone 3+ -> dislike
        $zone = strtolower($flood->flood_zone ?? '');
        $rules['flood_zone'] = [
            'assessment' => match (true) {
                str_contains($zone, '1') => 'like',
                str_contains($zone, '2') => 'neutral',
                str_contains($zone, '3') => 'dislike',
                default => null,
            },
            'auto_data' => ['source' => 'flood', 'flood_zone' => $flood->flood_zone],
        ];

        // Flood Warnings: 0 -> like, any -> dislike
        $warnings = $flood->active_warnings ?? [];
        $warningCount = is_array($warnings) ? count($warnings) : 0;
        $rules['flood_warnings'] = [
            'assessment' => $warningCount === 0 ? 'like' : 'dislike',
            'auto_data' => ['source' => 'flood', 'active_warning_count' => $warningCount],
        ];

        // Surface Water Risk: Low/Very Low -> like, Medium -> neutral, High -> dislike
        $surfaceWater = strtolower($flood->surface_water_risk ?? '');
        if ($surfaceWater !== '') {
            $rules['surface_water_risk'] = [
                'assessment' => match (true) {
                    in_array($surfaceWater, ['low', 'very low']) => 'like',
                    $surfaceWater === 'medium' => 'neutral',
                    in_array($surfaceWater, ['high', 'severe']) => 'dislike',
                    default => null,
                },
                'auto_data' => ['source' => 'flood', 'surface_water_risk' => $flood->surface_water_risk],
            ];
        }
    }

    private function assessCrimeRules($property, array &$rules): void
    {
        if ($property->crimeData->isEmpty()) {
            return;
        }

        // Overall Crime Level: < 10/month -> like, < 25 -> neutral, else -> dislike
        $months = $property->crimeData->pluck('month')->unique()->count();
        $totalCrime = $property->crimeData->sum('count');
        $avgPerMonth = $months > 0 ? $totalCrime / $months : 0;

        $rules['overall_crime_level'] = [
            'assessment' => match (true) {
                $avgPerMonth < 10 => 'like',
                $avgPerMonth < 25 => 'neutral',
                default => 'dislike',
            },
            'auto_data' => ['source' => 'police', 'avg_crimes_per_month' => round($avgPerMonth, 1)],
        ];

        // Burglary Rate: filter by 'burglary' category
        $burglaryData = $property->crimeData->filter(fn ($c) => strtolower($c->category) === 'burglary');
        if ($burglaryData->isNotEmpty()) {
            $burglaryTotal = $burglaryData->sum('count');
            $burglaryAvg = $months > 0 ? $burglaryTotal / $months : 0;

            $rules['burglary_rate'] = [
                'assessment' => match (true) {
                    $burglaryAvg < 2 => 'like',
                    $burglaryAvg < 5 => 'neutral',
                    default => 'dislike',
                },
                'auto_data' => ['source' => 'police', 'avg_burglaries_per_month' => round($burglaryAvg, 1)],
            ];
        }

        // Violent Crime: filter by 'violent-crime' or 'violence-and-sexual-offences'
        $violentData = $property->crimeData->filter(
            fn ($c) => in_array(strtolower($c->category), ['violent-crime', 'violence-and-sexual-offences'])
        );
        if ($violentData->isNotEmpty()) {
            $violentTotal = $violentData->sum('count');
            $violentAvg = $months > 0 ? $violentTotal / $months : 0;

            $rules['violent_crime'] = [
                'assessment' => match (true) {
                    $violentAvg < 3 => 'like',
                    $violentAvg < 8 => 'neutral',
                    default => 'dislike',
                },
                'auto_data' => ['source' => 'police', 'avg_violent_crimes_per_month' => round($violentAvg, 1)],
            ];
        }
    }

    private function assessPlanningRules($property, array &$rules): void
    {
        // Nearby Planning: pending count assessment
        if ($property->planningApplications->isNotEmpty()) {
            $pendingCount = $property->planningApplications
                ->filter(fn ($app) => in_array(strtolower($app->status ?? ''), ['pending', 'awaiting', 'under review']))
                ->count();

            $rules['nearby_planning'] = [
                'assessment' => match (true) {
                    $pendingCount === 0 => 'like',
                    $pendingCount <= 2 => 'neutral',
                    default => 'dislike',
                },
                'auto_data' => ['source' => 'planning', 'pending_applications' => $pendingCount],
            ];

            // Conservation Area: check if any application type/description mentions conservation
            $hasConservation = $property->planningApplications->contains(function ($app) {
                $type = strtolower($app->application_type ?? '');
                $desc = strtolower($app->description ?? '');

                return str_contains($type, 'conservation') || str_contains($desc, 'conservation area');
            });

            if ($hasConservation) {
                $rules['conservation_area'] = [
                    'assessment' => 'neutral',
                    'auto_data' => ['source' => 'planning', 'conservation_area_detected' => true],
                ];
            }

            // Listed Building: check if any application mentions listed building
            $hasListed = $property->planningApplications->contains(function ($app) {
                $type = strtolower($app->application_type ?? '');
                $desc = strtolower($app->description ?? '');

                return str_contains($type, 'listed building') || str_contains($desc, 'listed building');
            });

            if ($hasListed) {
                $rules['listed_building'] = [
                    'assessment' => 'neutral',
                    'auto_data' => ['source' => 'planning', 'listed_building_detected' => true],
                ];
            }
        } else {
            $rules['nearby_planning'] = [
                'assessment' => 'like',
                'auto_data' => ['source' => 'planning', 'pending_applications' => 0],
            ];
        }
    }

    private function assessLandRegistryRules($property, array &$rules): void
    {
        if (! $property->landRegistryData || ! $property->landRegistryData->fetched_at) {
            return;
        }

        $lr = $property->landRegistryData;

        // Freehold/Leasehold: freehold -> like, leasehold -> neutral
        $tenure = strtolower($lr->tenure ?? '');
        if ($tenure !== '') {
            $rules['freehold_leasehold'] = [
                'assessment' => match (true) {
                    str_contains($tenure, 'freehold') => 'like',
                    str_contains($tenure, 'leasehold') => 'neutral',
                    default => null,
                },
                'auto_data' => ['source' => 'land_registry', 'tenure' => $lr->tenure],
            ];
        }

        // Previous Sale Prices: if data exists, auto-like (information available)
        if ($lr->last_sold_price) {
            $rules['previous_sale_prices'] = [
                'assessment' => 'like',
                'auto_data' => [
                    'source' => 'land_registry',
                    'last_sold_price' => $lr->last_sold_price,
                    'last_sold_date' => $lr->last_sold_date?->format('Y-m-d'),
                ],
            ];
        }

        // Price vs Comparables: compare last_sold_price to area average from price_history
        $priceHistory = $lr->price_history ?? [];
        if (is_array($priceHistory) && count($priceHistory) >= 2 && $lr->last_sold_price) {
            $avgPrice = collect($priceHistory)->avg('price');
            if ($avgPrice > 0) {
                $ratio = $lr->last_sold_price / $avgPrice;
                $rules['price_vs_comparables'] = [
                    'assessment' => match (true) {
                        $ratio <= 0.95 => 'like',
                        $ratio <= 1.1 => 'neutral',
                        default => 'dislike',
                    },
                    'auto_data' => [
                        'source' => 'land_registry',
                        'last_sold_price' => $lr->last_sold_price,
                        'average_price' => round($avgPrice),
                        'ratio' => round($ratio, 2),
                    ],
                ];
            }
        }

        // Area Price Trend: analyse price_history array trend direction
        if (is_array($priceHistory) && count($priceHistory) >= 2) {
            $sorted = collect($priceHistory)->sortBy('date')->values();
            $lastTwo = $sorted->slice(-2)->values();
            $olderPrice = (int) ($lastTwo[0]['price'] ?? 0);
            $newerPrice = (int) ($lastTwo[1]['price'] ?? 0);

            if ($olderPrice > 0 && $newerPrice > 0) {
                $rules['area_price_trend'] = [
                    'assessment' => match (true) {
                        $newerPrice > $olderPrice => 'like',
                        $newerPrice === $olderPrice => 'neutral',
                        default => 'dislike',
                    },
                    'auto_data' => [
                        'source' => 'land_registry',
                        'older_price' => $olderPrice,
                        'newer_price' => $newerPrice,
                        'trend' => $newerPrice > $olderPrice ? 'rising' : ($newerPrice === $olderPrice ? 'stable' : 'falling'),
                    ],
                ];
            }
        }
    }
}

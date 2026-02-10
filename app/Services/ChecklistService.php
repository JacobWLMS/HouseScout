<?php

namespace App\Services;

use App\Models\PropertyAssessment;
use App\Models\SavedProperty;

class ChecklistService
{
    /**
     * Initialize checklist for a saved property — creates assessment records and runs auto-assessment.
     */
    public function initializeChecklist(SavedProperty $saved): void
    {
        $items = config('housescout.checklist.items', []);

        foreach ($items as $item) {
            PropertyAssessment::firstOrCreate(
                [
                    'saved_property_id' => $saved->id,
                    'item_key' => $item['key'],
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
     * Run auto-assessment based on API data.
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

        foreach ($autoRules as $itemKey => $assessment) {
            if ($assessment === null) {
                continue;
            }

            PropertyAssessment::query()
                ->where('saved_property_id', $saved->id)
                ->where('item_key', $itemKey)
                ->whereNull('assessment')
                ->update([
                    'assessment' => $assessment,
                    'is_auto_assessed' => true,
                ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getProgress(SavedProperty $saved): array
    {
        $items = config('housescout.checklist.items', []);
        $assessments = $saved->assessments()->pluck('assessment', 'item_key');

        $total = count($items);
        $assessed = $assessments->filter(fn ($a) => $a !== null)->count();
        $likes = $assessments->filter(fn ($a) => $a === 'like')->count();
        $dislikes = $assessments->filter(fn ($a) => $a === 'dislike')->count();
        $neutral = $assessments->filter(fn ($a) => $a === 'neutral')->count();

        $dealBreakerKeys = collect($items)
            ->filter(fn ($item) => $item['is_deal_breaker'] ?? false)
            ->pluck('key');

        $dealBreakers = $dealBreakerKeys->filter(fn ($key) => ($assessments[$key] ?? null) === 'dislike')->count();

        return [
            'total' => $total,
            'assessed' => $assessed,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'neutral' => $neutral,
            'dealBreakers' => $dealBreakers,
            'percentage' => $total > 0 ? (int) round(($assessed / $total) * 100) : 0,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function getAutoRules($property): array
    {
        $rules = [];

        // EPC Rating: A/B/C -> like, D -> neutral, E/F/G -> dislike
        if ($property->epcData && $property->epcData->fetched_at) {
            $rating = strtoupper($property->epcData->current_energy_rating ?? '');
            $rules['epc_rating'] = match (true) {
                in_array($rating, ['A', 'B', 'C']) => 'like',
                $rating === 'D' => 'neutral',
                in_array($rating, ['E', 'F', 'G']) => 'dislike',
                default => null,
            };

            // Running Costs: total < 1500 -> like, < 2500 -> neutral, else -> dislike
            $totalCost = (int) ($property->epcData->lighting_cost_current ?? 0)
                + (int) ($property->epcData->heating_cost_current ?? 0)
                + (int) ($property->epcData->hot_water_cost_current ?? 0);

            if ($totalCost > 0) {
                $rules['epc_costs'] = match (true) {
                    $totalCost < 1500 => 'like',
                    $totalCost < 2500 => 'neutral',
                    default => 'dislike',
                };
            }
        }

        // Flood Risk: Low/Very Low -> like, Medium -> neutral, High -> dislike
        if ($property->floodRiskData && $property->floodRiskData->fetched_at) {
            $level = strtolower($property->floodRiskData->flood_risk_level ?? '');
            $rules['flood_risk'] = match (true) {
                in_array($level, ['low', 'very low']) => 'like',
                $level === 'medium' => 'neutral',
                in_array($level, ['high', 'severe']) => 'dislike',
                default => null,
            };

            // Flood Warnings: 0 -> like, any -> dislike
            $warnings = $property->floodRiskData->active_warnings ?? [];
            $warningCount = is_array($warnings) ? count($warnings) : 0;
            $rules['flood_warnings'] = $warningCount === 0 ? 'like' : 'dislike';
        }

        // Crime: < 10/month -> like, < 25 -> neutral, else -> dislike
        if ($property->crimeData->isNotEmpty()) {
            $months = $property->crimeData->pluck('month')->unique()->count();
            $total = $property->crimeData->sum('count');
            $avgPerMonth = $months > 0 ? $total / $months : 0;

            $rules['crime_level'] = match (true) {
                $avgPerMonth < 10 => 'like',
                $avgPerMonth < 25 => 'neutral',
                default => 'dislike',
            };
        }

        // Planning: 0 pending -> like, 1-2 -> neutral, 3+ -> dislike
        if ($property->planningApplications->isNotEmpty()) {
            $pendingCount = $property->planningApplications
                ->filter(fn ($app) => in_array(strtolower($app->status ?? ''), ['pending', 'awaiting', 'under review']))
                ->count();

            $rules['planning_issues'] = match (true) {
                $pendingCount === 0 => 'like',
                $pendingCount <= 2 => 'neutral',
                default => 'dislike',
            };
        } else {
            $rules['planning_issues'] = 'like';
        }

        // Price History: rising -> like, stable -> neutral, falling -> dislike
        if ($property->landRegistryData && $property->landRegistryData->fetched_at) {
            $priceHistory = $property->landRegistryData->price_history ?? [];
            if (is_array($priceHistory) && count($priceHistory) >= 2) {
                $sorted = collect($priceHistory)->sortBy('date')->values();
                $lastTwo = $sorted->slice(-2)->values();
                $olderPrice = (int) ($lastTwo[0]['price'] ?? 0);
                $newerPrice = (int) ($lastTwo[1]['price'] ?? 0);

                if ($olderPrice > 0 && $newerPrice > 0) {
                    $rules['price_history'] = match (true) {
                        $newerPrice > $olderPrice => 'like',
                        $newerPrice === $olderPrice => 'neutral',
                        default => 'dislike',
                    };
                }
            }
        }

        return $rules;
    }
}

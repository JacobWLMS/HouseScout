<?php

namespace App\Filament\Widgets;

use App\Models\PropertySearch;
use App\Models\SavedProperty;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalSearches = PropertySearch::query()
            ->where('user_id', $userId)
            ->count();

        $totalSaved = SavedProperty::query()
            ->where('user_id', $userId)
            ->count();

        $mostSearchedArea = PropertySearch::query()
            ->where('property_searches.user_id', $userId)
            ->join('properties', 'property_searches.property_id', '=', 'properties.id')
            ->select(DB::raw("SPLIT_PART(properties.postcode, ' ', 1) as outcode"), DB::raw('COUNT(*) as search_count'))
            ->groupBy('outcode')
            ->orderByDesc('search_count')
            ->value('outcode') ?? 'N/A';

        return [
            Stat::make('Total Searches', $totalSearches)
                ->description('All time')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('primary'),
            Stat::make('Saved Properties', $totalSaved)
                ->description('Your collection')
                ->descriptionIcon('heroicon-m-bookmark')
                ->color('success'),
            Stat::make('Top Postcode Area', $mostSearchedArea)
                ->description('Most searched')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
        ];
    }
}

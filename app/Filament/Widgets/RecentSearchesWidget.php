<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\PropertyDetailPage;
use App\Models\PropertySearch;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class RecentSearchesWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PropertySearch::query()
                    ->where('user_id', Auth::id())
                    ->with('property')
                    ->latest('searched_at')
                    ->limit(5)
            )
            ->heading('Recent Searches')
            ->columns([
                TextColumn::make('property.postcode')
                    ->label('Postcode')
                    ->searchable(),
                TextColumn::make('search_query')
                    ->label('Search Query')
                    ->limit(30),
                TextColumn::make('searched_at')
                    ->label('Date Searched')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('demand_count')
                    ->label('Demand (30d)')
                    ->state(function (PropertySearch $record): int {
                        return $record->property
                            ->propertySearches()
                            ->where('searched_at', '>=', now()->subDays(30))
                            ->distinct('user_id')
                            ->count('user_id');
                    })
                    ->badge()
                    ->color('primary'),
            ])
            ->recordUrl(fn (PropertySearch $record): string => PropertyDetailPage::getUrl(['property' => $record->property_id]))
            ->paginated(false);
    }
}

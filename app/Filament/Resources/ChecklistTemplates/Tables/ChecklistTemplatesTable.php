<?php

namespace App\Filament\Resources\ChecklistTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ChecklistTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->numeric(),
                TextColumn::make('category_label')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state, $record): string => match ($record->category) {
                        'flood_environmental' => 'danger',
                        'price_value' => 'success',
                        'energy_condition' => 'warning',
                        'legal_title' => 'info',
                        'planning_building' => 'primary',
                        'crime' => 'danger',
                        'schools' => 'success',
                        'connectivity' => 'info',
                        'neighbourhood' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'deal_breaker' => 'danger',
                        'important' => 'warning',
                        'nice_to_have' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deal_breaker' => 'Deal Breaker',
                        'important' => 'Important',
                        'nice_to_have' => 'Nice to Have',
                        default => $state,
                    }),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'automated' => 'info',
                        'manual' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('guidance')
                    ->limit(50)
                    ->placeholder('—'),
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'flood_environmental' => 'Flood & Environmental',
                        'price_value' => 'Price & Value',
                        'energy_condition' => 'Energy & Condition',
                        'legal_title' => 'Legal & Title',
                        'planning_building' => 'Planning & Building',
                        'crime' => 'Crime',
                        'schools' => 'Schools',
                        'connectivity' => 'Connectivity',
                        'neighbourhood' => 'Neighbourhood',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'deal_breaker' => 'Deal Breaker',
                        'important' => 'Important',
                        'nice_to_have' => 'Nice to Have',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'automated' => 'Automated',
                        'manual' => 'Manual',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

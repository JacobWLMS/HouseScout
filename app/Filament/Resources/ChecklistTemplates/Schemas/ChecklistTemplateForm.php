<?php

namespace App\Filament\Resources\ChecklistTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChecklistTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        Select::make('category')
                            ->options(static::categoryOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if ($state) {
                                    $set('category_label', static::categoryOptions()[$state] ?? '');
                                }
                            }),
                        TextInput::make('category_label')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->maxLength(255),
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Classification')
                    ->schema([
                        Select::make('severity')
                            ->options([
                                'deal_breaker' => 'Deal Breaker',
                                'important' => 'Important',
                                'nice_to_have' => 'Nice to Have',
                            ])
                            ->required(),
                        Select::make('type')
                            ->options([
                                'automated' => 'Automated',
                                'manual' => 'Manual',
                            ])
                            ->required()
                            ->live(),
                    ])
                    ->columns(2),
                Section::make('Guidance')
                    ->schema([
                        Textarea::make('guidance')
                            ->rows(3)
                            ->maxLength(1000),
                        TextInput::make('link')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->visible(fn (callable $get): bool => $get('type') === 'manual'),
                Section::make('Settings')
                    ->schema([
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function categoryOptions(): array
    {
        return [
            'flood_environmental' => 'Flood & Environmental',
            'price_value' => 'Price & Value',
            'energy_condition' => 'Energy & Condition',
            'legal_title' => 'Legal & Title',
            'planning_building' => 'Planning & Building',
            'crime' => 'Crime',
            'schools' => 'Schools',
            'connectivity' => 'Connectivity',
            'neighbourhood' => 'Neighbourhood',
        ];
    }
}

<?php

namespace App\Filament\Resources\SavedProperties;

use App\Filament\Pages\PropertyDetailPage;
use App\Filament\Resources\SavedProperties\Pages\ManageSavedProperties;
use App\Models\SavedProperty;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavedPropertyResource extends Resource
{
    protected static ?string $model = SavedProperty::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected static ?string $navigationLabel = 'Saved Properties';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['property.epcData']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.address_line_1')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('property.postcode')
                    ->label('Postcode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('property.epcData.current_energy_rating')
                    ->label('EPC Rating')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'success',
                        'C' => 'success',
                        'D' => 'warning',
                        'E' => 'warning',
                        'F' => 'danger',
                        'G' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->placeholder('No notes'),
                TextColumn::make('created_at')
                    ->label('Date Saved')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view_property')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (SavedProperty $record): string => PropertyDetailPage::getUrl(['property' => $record->property_id])),
                DeleteAction::make()
                    ->label('Remove'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Remove Selected'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSavedProperties::route('/'),
        ];
    }
}

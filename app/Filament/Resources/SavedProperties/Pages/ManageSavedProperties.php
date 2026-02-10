<?php

namespace App\Filament\Resources\SavedProperties\Pages;

use App\Filament\Resources\SavedProperties\SavedPropertyResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSavedProperties extends ManageRecords
{
    protected static string $resource = SavedPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

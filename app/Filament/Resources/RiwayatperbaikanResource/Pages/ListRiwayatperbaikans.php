<?php

namespace App\Filament\Resources\RiwayatperbaikanResource\Pages;

use App\Filament\Resources\RiwayatperbaikanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatperbaikans extends ListRecords
{
    protected static string $resource = RiwayatperbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}

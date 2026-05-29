<?php

namespace App\Filament\Resources\PerbaikanResource\Pages;

use App\Filament\Resources\PerbaikanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerbaikans extends ListRecords
{
    protected static string $resource = PerbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\LaporanKeluhanResource\Pages;

use App\Filament\Resources\LaporanKeluhanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanKeluhans extends ListRecords
{
    protected static string $resource = LaporanKeluhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

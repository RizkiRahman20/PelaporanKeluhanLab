<?php

namespace App\Filament\Resources\RiwayatperbaikanResource\Pages;

use App\Filament\Resources\RiwayatperbaikanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatperbaikan extends EditRecord
{
    protected static string $resource = RiwayatperbaikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

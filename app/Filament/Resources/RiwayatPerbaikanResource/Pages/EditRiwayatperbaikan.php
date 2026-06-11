<?php

namespace App\Filament\Resources\RiwayatPerbaikanResource\Pages;

use App\Filament\Resources\RiwayatPerbaikanResource;
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

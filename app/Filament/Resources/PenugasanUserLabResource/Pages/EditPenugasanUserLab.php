<?php

namespace App\Filament\Resources\PenugasanUserLabResource\Pages;

use App\Filament\Resources\PenugasanUserLabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenugasanUserLab extends EditRecord
{
    protected static string $resource = PenugasanUserLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

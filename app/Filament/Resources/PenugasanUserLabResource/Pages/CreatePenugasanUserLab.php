<?php

namespace App\Filament\Resources\PenugasanUserLabResource\Pages;

use App\Filament\Resources\PenugasanUserLabResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenugasanUserLab extends CreateRecord
{
    protected static string $resource = PenugasanUserLabResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

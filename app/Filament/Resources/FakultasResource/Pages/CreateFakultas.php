<?php

namespace App\Filament\Resources\FakultasResource\Pages;

use App\Filament\Resources\FakultasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFakultas extends CreateRecord
{
    protected static string $resource = FakultasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

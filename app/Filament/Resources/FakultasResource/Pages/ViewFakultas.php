<?php

namespace App\Filament\Resources\FakultasResource\Pages;

use App\Filament\Resources\FakultasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFakultas extends ViewRecord
{
    protected static string $resource = FakultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

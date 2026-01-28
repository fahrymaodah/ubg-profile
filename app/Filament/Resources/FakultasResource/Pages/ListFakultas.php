<?php

namespace App\Filament\Resources\FakultasResource\Pages;

use App\Filament\Resources\FakultasResource;
use App\Services\AppConfigService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFakultas extends ListRecords
{
    protected static string $resource = FakultasResource::class;

    protected function getHeaderActions(): array
    {
        $configService = app(AppConfigService::class);
        $canCreate = $configService->canCreateFakultas();

        return [
            Actions\CreateAction::make()
                ->visible($canCreate),
        ];
    }
}

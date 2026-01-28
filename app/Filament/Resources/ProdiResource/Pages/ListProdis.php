<?php

namespace App\Filament\Resources\ProdiResource\Pages;

use App\Filament\Resources\ProdiResource;
use App\Services\AppConfigService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProdis extends ListRecords
{
    protected static string $resource = ProdiResource::class;

    protected function getHeaderActions(): array
    {
        $configService = app(AppConfigService::class);
        $canCreate = $configService->canCreateProdi();

        return [
            Actions\CreateAction::make()
                ->visible($canCreate),
        ];
    }
}

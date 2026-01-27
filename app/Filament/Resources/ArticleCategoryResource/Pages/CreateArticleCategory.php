<?php

namespace App\Filament\Resources\ArticleCategoryResource\Pages;

use App\Enums\UnitType;
use App\Filament\Resources\ArticleCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            $data['unit_type'] = UnitType::UNIVERSITAS;
            $data['unit_id'] = null;
        } else {
            $data['unit_type'] = $user->unit_type;
            $data['unit_id'] = $user->unit_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

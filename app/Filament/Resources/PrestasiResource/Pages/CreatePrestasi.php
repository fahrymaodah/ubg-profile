<?php

namespace App\Filament\Resources\PrestasiResource\Pages;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\PrestasiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePrestasi extends CreateRecord
{
    protected static string $resource = PrestasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        if ($user->role === UserRole::PRODI) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        
        return $data;
    }
}

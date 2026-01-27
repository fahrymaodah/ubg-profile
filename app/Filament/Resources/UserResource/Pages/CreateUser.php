<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Enums\UnitType;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roleValue = isset($data['role']) 
            ? (is_object($data['role']) ? $data['role']->value : $data['role'])
            : null;
        
        [$data['unit_type'], $data['unit_id']] = match($roleValue) {
            'fakultas' => [UnitType::FAKULTAS->value, $data['fakultas_id'] ?? null],
            'prodi' => [UnitType::PRODI->value, $data['prodi_id'] ?? null],
            'universitas' => [UnitType::UNIVERSITAS->value, null],
            default => [null, null],
        };
        
        unset($data['fakultas_id'], $data['prodi_id']);
        
        return $data;
    }
}

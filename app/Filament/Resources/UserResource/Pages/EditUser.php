<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Enums\UnitType;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['role']) && isset($data['unit_id'])) {
            $roleValue = is_object($data['role']) ? $data['role']->value : $data['role'];
            
            if ($roleValue === 'fakultas') {
                $data['fakultas_id'] = $data['unit_id'];
            } elseif ($roleValue === 'prodi') {
                $data['prodi_id'] = $data['unit_id'];
            }
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $roleValue = $this->getRoleValue($data);
        [$data['unit_type'], $data['unit_id']] = $this->resolveUnitData($roleValue, $data);
        
        unset($data['fakultas_id'], $data['prodi_id']);
        
        return $data;
    }

    private function getRoleValue(array $data): string
    {
        if (isset($data['role'])) {
            return is_object($data['role']) ? $data['role']->value : $data['role'];
        }
        
        return $this->record->role instanceof \BackedEnum 
            ? $this->record->role->value 
            : $this->record->role;
    }

    private function resolveUnitData(string $roleValue, array $data): array
    {
        return match($roleValue) {
            'fakultas' => [
                UnitType::FAKULTAS->value,
                (isset($data['fakultas_id']) && $data['fakultas_id'] !== null) 
                    ? $data['fakultas_id'] 
                    : $this->record->unit_id
            ],
            'prodi' => [
                UnitType::PRODI->value,
                (isset($data['prodi_id']) && $data['prodi_id'] !== null) 
                    ? $data['prodi_id'] 
                    : $this->record->unit_id
            ],
            'universitas' => [UnitType::UNIVERSITAS->value, null],
            default => [null, null],
        };
    }
}

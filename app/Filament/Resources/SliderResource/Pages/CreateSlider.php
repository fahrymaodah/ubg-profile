<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\SliderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSlider extends CreateRecord
{
    protected static string $resource = SliderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // Auto-set unit untuk prodi (field hidden)
        if ($user->role === UserRole::PRODI) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        
        return $data;
    }
}

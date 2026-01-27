<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

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

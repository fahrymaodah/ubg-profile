<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\GalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditGallery extends EditRecord
{
    protected static string $resource = GalleryResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        
        if ($user->role === UserRole::PRODI) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        
        return $data;
    }
}

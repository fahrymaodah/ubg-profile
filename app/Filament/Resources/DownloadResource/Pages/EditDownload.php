<?php

namespace App\Filament\Resources\DownloadResource\Pages;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Filament\Resources\DownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditDownload extends EditRecord
{
    protected static string $resource = DownloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        
        // Preserve unit untuk prodi
        if ($user->role === UserRole::PRODI) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        
        // Recalculate file size if file is changed
        if (!empty($data['file'])) {
            $path = Storage::disk('public')->path($data['file']);
            if (file_exists($path)) {
                $data['file_size'] = filesize($path);
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

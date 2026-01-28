<?php

namespace App\Filament\Resources\ProdiResource\Pages;

use App\Filament\Resources\ProdiResource;
use App\Services\AppConfigService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProdi extends CreateRecord
{
    protected static string $resource = ProdiResource::class;

    public function mount(): void
    {
        $configService = app(AppConfigService::class);
        
        if (!$configService->canCreateProdi()) {
            $status = $configService->getStatus();
            $max = $status['usage']['prodi']['max'] ?? 0;
            $email = $status['developer']['email'] ?? 'developer';
            
            Notification::make()
                ->title('Batas Lisensi Tercapai')
                ->body("Maksimal {$max} program studi. Hubungi {$email} untuk upgrade.")
                ->warning()
                ->persistent()
                ->send();
            
            $this->redirect(static::getResource()::getUrl('index'));
            return;
        }
        
        parent::mount();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\FakultasResource\Pages;

use App\Filament\Resources\FakultasResource;
use App\Services\AppConfigService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFakultas extends CreateRecord
{
    protected static string $resource = FakultasResource::class;

    public function mount(): void
    {
        $configService = app(AppConfigService::class);
        
        if (!$configService->canCreateFakultas()) {
            $status = $configService->getStatus();
            $max = $status['usage']['fakultas']['max'] ?? 0;
            $email = $status['developer']['email'] ?? 'developer';
            
            Notification::make()
                ->title('Batas Lisensi Tercapai')
                ->body("Maksimal {$max} fakultas. Hubungi {$email} untuk upgrade.")
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

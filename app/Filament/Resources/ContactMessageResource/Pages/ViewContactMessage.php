<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAsRead')
                ->label('Tandai Dibaca')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'unread')
                ->action(function () {
                    $this->record->markAsRead();
                    Notification::make()
                        ->title('Pesan ditandai sebagai dibaca')
                        ->success()
                        ->send();
                }),

            Action::make('reply')
                ->label('Balas via Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->url(fn () => "mailto:{$this->record->email}?subject=Re: {$this->record->subject}")
                ->openUrlInNewTab()
                ->after(function () {
                    $this->record->markAsReplied();
                }),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Mark as read when viewing
        if ($this->record->status === 'unread') {
            $this->record->markAsRead();
        }

        return $data;
    }
}

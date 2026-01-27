<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Enums\UnitType;
use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // Set author
        $data['author_id'] = $user->id;

        // Untuk prodi, paksa unit mereka sendiri
        if ($user->isProdi()) {
            $data['unit_type'] = UnitType::PRODI->value;
            $data['unit_id'] = $user->unit_id;
        }
        // Untuk fakultas, paksa unit mereka jika tidak memilih prodi
        elseif ($user->isFakultas()) {
            // Jika memilih fakultas, paksa fakultas mereka sendiri
            if (($data['unit_type'] ?? null) === UnitType::FAKULTAS->value) {
                $data['unit_id'] = $user->unit_id;
            }
            // Prodi yang dipilih harus di bawah fakultas mereka (sudah difilter di form)
        }
        // SuperAdmin dan Universitas menggunakan data dari form
        // unit_type dan unit_id sudah ada dari form

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

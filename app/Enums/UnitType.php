<?php

namespace App\Enums;

enum UnitType: string
{
    case UNIVERSITAS = 'universitas';
    case FAKULTAS = 'fakultas';
    case PRODI = 'prodi';

    public function label(): string
    {
        return match ($this) {
            self::UNIVERSITAS => 'Universitas',
            self::FAKULTAS => 'Fakultas',
            self::PRODI => 'Program Studi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNIVERSITAS => 'primary',
            self::FAKULTAS => 'success',
            self::PRODI => 'info',
        };
    }

    /**
     * Get the model class for this unit type
     */
    public function modelClass(): ?string
    {
        return match ($this) {
            self::UNIVERSITAS => null, // No model, uses settings
            self::FAKULTAS => \App\Models\Fakultas::class,
            self::PRODI => \App\Models\Prodi::class,
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toSelectOptions(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}

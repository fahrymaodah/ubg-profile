<?php

namespace App\Enums;

enum Jenjang: string
{
    case D3 = 'D3';
    case D4 = 'D4';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';

    public function label(): string
    {
        return match ($this) {
            self::D3 => 'Diploma 3 (D3)',
            self::D4 => 'Diploma 4 (D4)',
            self::S1 => 'Sarjana (S1)',
            self::S2 => 'Magister (S2)',
            self::S3 => 'Doktor (S3)',
        };
    }

    public function shortLabel(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::D3 => 'info',
            self::D4 => 'info',
            self::S1 => 'primary',
            self::S2 => 'success',
            self::S3 => 'danger',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::D3 => 1,
            self::D4 => 2,
            self::S1 => 3,
            self::S2 => 4,
            self::S3 => 5,
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

<?php

namespace App\Enums;

enum PrestasiTingkat: string
{
    case INTERNASIONAL = 'internasional';
    case NASIONAL = 'nasional';
    case REGIONAL = 'regional';
    case LOKAL = 'lokal';

    public function label(): string
    {
        return match ($this) {
            self::INTERNASIONAL => 'Internasional',
            self::NASIONAL => 'Nasional',
            self::REGIONAL => 'Regional',
            self::LOKAL => 'Lokal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INTERNASIONAL => 'danger',
            self::NASIONAL => 'primary',
            self::REGIONAL => 'success',
            self::LOKAL => 'info',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::INTERNASIONAL => 1,
            self::NASIONAL => 2,
            self::REGIONAL => 3,
            self::LOKAL => 4,
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

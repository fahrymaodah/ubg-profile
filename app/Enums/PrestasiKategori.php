<?php

namespace App\Enums;

enum PrestasiKategori: string
{
    case AKADEMIK = 'akademik';
    case NON_AKADEMIK = 'non_akademik';
    case PENELITIAN = 'penelitian';
    case PENGABDIAN = 'pengabdian';
    case OLAHRAGA = 'olahraga';
    case SENI = 'seni';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::AKADEMIK => 'Akademik',
            self::NON_AKADEMIK => 'Non-Akademik',
            self::PENELITIAN => 'Penelitian',
            self::PENGABDIAN => 'Pengabdian',
            self::OLAHRAGA => 'Olahraga',
            self::SENI => 'Seni',
            self::LAINNYA => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AKADEMIK => 'primary',
            self::NON_AKADEMIK => 'info',
            self::PENELITIAN => 'success',
            self::PENGABDIAN => 'warning',
            self::OLAHRAGA => 'danger',
            self::SENI => 'gray',
            self::LAINNYA => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AKADEMIK => 'heroicon-o-academic-cap',
            self::NON_AKADEMIK => 'heroicon-o-trophy',
            self::PENELITIAN => 'heroicon-o-beaker',
            self::PENGABDIAN => 'heroicon-o-hand-raised',
            self::OLAHRAGA => 'heroicon-o-bolt',
            self::SENI => 'heroicon-o-paint-brush',
            self::LAINNYA => 'heroicon-o-star',
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

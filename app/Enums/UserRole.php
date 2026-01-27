<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case UNIVERSITAS = 'universitas';
    case FAKULTAS = 'fakultas';
    case PRODI = 'prodi';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'SUPERADMIN',
            self::UNIVERSITAS => 'UNIVERSITAS',
            self::FAKULTAS => 'FAKULTAS',
            self::PRODI => 'PRODI',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'danger',
            self::UNIVERSITAS => 'primary',
            self::FAKULTAS => 'success',
            self::PRODI => 'info',
        };
    }

    /**
     * Check if this role can manage the given role
     */
    public function canManage(UserRole $role): bool
    {
        return match ($this) {
            self::SUPERADMIN => true,
            self::UNIVERSITAS => in_array($role, [self::FAKULTAS, self::PRODI]),
            self::FAKULTAS => $role === self::PRODI,
            self::PRODI => false,
        };
    }

    /**
     * Get roles that this role can create
     */
    public function creatableRoles(): array
    {
        return match ($this) {
            self::SUPERADMIN => [self::SUPERADMIN, self::UNIVERSITAS, self::FAKULTAS, self::PRODI],
            self::UNIVERSITAS => [self::FAKULTAS, self::PRODI],
            self::FAKULTAS => [self::PRODI],
            self::PRODI => [],
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

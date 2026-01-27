<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view settings
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
            UserRole::PRODI,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Setting $setting): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check if user can manage this unit
        return $setting->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can update settings.
     */
    public function update(User $user, Setting $setting): bool
    {
        // Super admin can update all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Only admins can update settings
        if (!in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
            UserRole::PRODI,
        ])) {
            return false;
        }

        // Check if user can manage this unit
        return $setting->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can manage settings for a specific unit.
     */
    public function manageForUnit(User $user, $unitType, $unitId): bool
    {
        // Super admin can manage all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Only admins can manage settings
        if (!in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
            UserRole::PRODI,
        ])) {
            return false;
        }

        // Check if user can manage this unit
        return $user->canManageUnit($unitType, $unitId);
    }
}

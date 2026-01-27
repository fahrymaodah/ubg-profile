<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Fakultas;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FakultasPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fakultas $fakultas): bool
    {
        // Super admin and universitas admin can view all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin can view their own fakultas
        if ($user->isFakultas()) {
            return $user->unit_id === $fakultas->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fakultas $fakultas): bool
    {
        // Super admin and universitas admin can update all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin can update their own fakultas
        if ($user->isFakultas()) {
            return $user->unit_id === $fakultas->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fakultas $fakultas): bool
    {
        // Only super admin and universitas admin can delete
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fakultas $fakultas): bool
    {
        return $this->delete($user, $fakultas);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fakultas $fakultas): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can publish the fakultas.
     */
    public function publish(User $user, Fakultas $fakultas): bool
    {
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
        ]);
    }
}

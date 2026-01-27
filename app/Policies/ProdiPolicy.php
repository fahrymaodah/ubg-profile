<?php

namespace App\Policies;

use App\Enums\UnitType;
use App\Enums\UserRole;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProdiPolicy
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
            UserRole::PRODI,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Prodi $prodi): bool
    {
        // Super admin and universitas admin can view all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin can view prodi in their fakultas
        if ($user->isFakultas()) {
            return $prodi->fakultas_id === $user->unit_id;
        }

        // Prodi admin can view their own prodi
        if ($user->isProdi()) {
            return $user->unit_id === $prodi->id;
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
            UserRole::FAKULTAS,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prodi $prodi): bool
    {
        // Super admin and universitas admin can update all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin can update prodi in their fakultas
        if ($user->isFakultas()) {
            return $prodi->fakultas_id === $user->unit_id;
        }

        // Prodi admin can update their own prodi
        if ($user->isProdi()) {
            return $user->unit_id === $prodi->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prodi $prodi): bool
    {
        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
        ]) && $this->view($user, $prodi);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Prodi $prodi): bool
    {
        return $this->delete($user, $prodi);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Prodi $prodi): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can publish the prodi.
     */
    public function publish(User $user, Prodi $prodi): bool
    {
        // Fakultas admin can publish prodi in their fakultas
        if ($user->isFakultas()) {
            return $prodi->fakultas_id === $user->unit_id;
        }

        return in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
        ]);
    }
}

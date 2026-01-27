<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DosenPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view dosen
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dosen $dosen): bool
    {
        // Super admin and universitas admin can view all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin can view dosen in their fakultas
        if ($user->isFakultas()) {
            return $dosen->prodi && $dosen->prodi->fakultas_id === $user->unit_id;
        }

        // Prodi admin can view dosen in their prodi
        if ($user->isProdi()) {
            return $dosen->prodi_id === $user->unit_id;
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
            UserRole::PRODI,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dosen $dosen): bool
    {
        // Super admin and universitas admin can update all
        if ($user->isSuperAdmin() || $user->isUniversitas()) {
            return true;
        }

        // Fakultas admin/editor can update dosen in their fakultas
        if ($user->isFakultas()) {
            return $dosen->prodi && $dosen->prodi->fakultas_id === $user->unit_id;
        }

        // Prodi admin/editor can update dosen in their prodi
        if ($user->isProdi()) {
            return $dosen->prodi_id === $user->unit_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dosen $dosen): bool
    {
        // Only admins can delete
        if (!in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
            UserRole::PRODI,
        ])) {
            return false;
        }

        return $this->view($user, $dosen);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dosen $dosen): bool
    {
        return $this->delete($user, $dosen);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dosen $dosen): bool
    {
        return $user->isSuperAdmin();
    }
}

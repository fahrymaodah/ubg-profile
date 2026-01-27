<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
    public function view(User $user, User $model): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Admin can view users in their unit
        return $user->canManageUnit($model->unit_type, $model->unit_id);
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
    public function update(User $user, User $model): bool
    {
        // Super admin can update all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can update themselves (except role)
        if ($user->id === $model->id) {
            return true;
        }

        // Cannot edit super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        // Admin can update users they can manage
        return $user->canManageUnit($model->unit_type, $model->unit_id) 
            && $user->role->canManage($model->role);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        // Super admin can delete all except themselves
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Cannot delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        // Admin can delete users they can manage
        return $user->canManageUnit($model->unit_type, $model->unit_id)
            && $user->role->canManage($model->role);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can change the model's role.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Cannot change own role
        if ($user->id === $model->id) {
            return false;
        }

        // Super admin can change all roles
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Must be able to manage the user
        if (!$user->canManageUnit($model->unit_type, $model->unit_id)) {
            return false;
        }

        // Can only assign roles that user can manage
        return $user->role->canManage($model->role);
    }
}

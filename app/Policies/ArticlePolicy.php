<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view articles
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Article $article): bool
    {
        // Super admin can view all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check if user can manage this unit
        return $article->isAccessibleBy($user);
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
    public function update(User $user, Article $article): bool
    {
        // Super admin can update all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check if user can manage this unit
        return $article->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        // Super admin can delete all
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Only admins can delete
        if (!in_array($user->role, [
            UserRole::SUPERADMIN,
            UserRole::UNIVERSITAS,
            UserRole::FAKULTAS,
            UserRole::PRODI,
        ])) {
            return false;
        }

        // Check if user can manage this unit
        return $article->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        return $this->delete($user, $article);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }

    /**
     * Determine whether the user can feature the model.
     */
    public function feature(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }
}

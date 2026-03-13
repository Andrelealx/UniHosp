<?php

namespace App\Policies;

use App\Models\Painel;
use App\Models\User;

class PainelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('paineis.view') || $user->can('paineis.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Painel $painel): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('paineis.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Painel $painel): bool
    {
        return $user->can('paineis.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Painel $painel): bool
    {
        return $user->can('paineis.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Painel $painel): bool
    {
        return $user->can('paineis.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Painel $painel): bool
    {
        return $user->hasRole('administrador');
    }
}

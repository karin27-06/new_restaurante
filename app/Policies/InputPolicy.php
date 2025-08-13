<?php

namespace App\Policies;

use App\Models\Input;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InputPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver insumos');     
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Input $input): bool
    {
        return $user->can('ver insumos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear insumos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Input $input): bool
    {
        return $user->can('editar insumos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Input $input): bool
    {
        return $user->can('eliminar insumos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Input $input): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Input $input): bool
    {
        return false;
    }
}

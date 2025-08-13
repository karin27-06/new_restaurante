<?php

namespace App\Policies;

use App\Models\Areas;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AreasPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver areas');  
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Areas $areas): bool
    {
        return $user->can('ver areas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear areas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Areas $areas): bool
    {
        return $user->can('editar areas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Areas $areas): bool
    {
        return $user->can('eliminar areas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Areas $areas): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Areas $areas): bool
    {
        return false;
    }
}

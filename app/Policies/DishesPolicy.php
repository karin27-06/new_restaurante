<?php

namespace App\Policies;

use App\Models\Dishes;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DishesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver dishes');  
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dishes $dishes): bool
    {
        return $user->can('ver dishes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear dishes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dishes $dishes): bool
    {
        return $user->can('editar dishes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dishes $dishes): bool
    {
        return $user->can('eliminar dishes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dishes $dishes): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dishes $dishes): bool
    {
        return false;
    }
}

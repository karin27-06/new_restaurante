<?php

namespace App\Policies;

use App\Models\MovementInput;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MovementInputPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver facturas insumos');     
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MovementInput $movementInput): bool
    {
        return $user->can('ver facturas insumos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear facturas insumos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MovementInput $movementInput): bool
    {
        return $user->can('editar facturas insumos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MovementInput $movementInput): bool
    {
        return $user->can('eliminar facturas insumos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MovementInput $movementInput): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MovementInput $movementInput): bool
    {
        return false;
    }
}

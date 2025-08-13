<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver ventas');  
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sale $venta): bool
    {
        return $user->can('ver ventas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear ventas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sale $venta): bool
    {
        return $user->can('editar ventas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $venta): bool
    {
        return $user->can('eliminar ventas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sale $venta): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sale $venta): bool
    {
        return true;
    }
}

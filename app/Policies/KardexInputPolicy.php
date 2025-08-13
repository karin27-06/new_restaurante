<?php

namespace App\Policies;

use App\Models\KardexInput;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KardexInputPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver kardex insumos');     
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KardexInput $kardexinput): bool
    {
        return $user->can('ver kardex insumos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear kardex insumos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KardexInput $kardexinput): bool
    {
        return $user->can('editar kardex insumos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KardexInput $kardexinput): bool
    {
        return $user->can('eliminar kardex insumos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KardexInput $kardexinput): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KardexInput $kardexinput): bool
    {
        return false;
    }
}

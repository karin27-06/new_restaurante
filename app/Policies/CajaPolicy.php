<?php

namespace App\Policies;

use App\Models\Caja;
use App\Models\User;

class CajaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver cajas');
    }

    public function view(User $user, Caja $caja): bool
    {
        return $user->can('ver cajas');
    }

    public function create(User $user): bool
    {
        return $user->can('crear cajas');
    }

    public function update(User $user, Caja $caja): bool
    {
        return $user->can('editar cajas');
    }

    public function delete(User $user, Caja $caja): bool
    {
        return $user->can('eliminar cajas');
    }
    public function restore(User $user, Caja $caja): bool
    {
        return false;
    }
    public function forceDelete(User $user, Caja $caja): bool
    {
        return false;
    }
}

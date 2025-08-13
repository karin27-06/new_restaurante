<?php

namespace App\Policies;

use App\Models\EmployeeType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeeTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver tipos_empleados');  
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeType $employeeType): bool
    {
        return $user->can('ver tipos_empleados');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear tipos_empleados');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeType $employeeType): bool
    {
        return $user->can('editar tipos_empleados');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeType $employeeType): bool
    {
        return $user->can('eliminar tipos_empleados');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeeType $employeeType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeeType $employeeType): bool
    {
        return true;
    }
}

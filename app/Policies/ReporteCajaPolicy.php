<?php

namespace App\Policies;

use App\Models\ReporteCaja;
use App\Models\User;

class ReporteCajaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver reporte_cajas');
    }
    public function view(User $user, ReporteCaja $reporte_caja): bool
    {
        return $user->can('ver reporte_cajas');
    }
    public function update(User $user, ReporteCaja $reporte_caja): bool
    {
        return $user->can('editar reporte_cajas');
    }
    public function create(User $user): bool
    {
        return false;
    }
    public function delete(User $user, ReporteCaja $reporte_caja): bool
    {
        return false;
    }
    public function restore(User $user, ReporteCaja $reporte_caja): bool
    {
        return false;
    }
    public function forceDelete(User $user, ReporteCaja $reporte_caja): bool
    {
        return false;
    }
}

<?php
namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByDateKardex
{
    public function __construct(private $startDate, private $endDate)
    {
        // Se pueden pasar las fechas al constructor para hacer las validaciones
    }

    public function __invoke(Builder $builder, Closure $next)
    {
        // Si existe una fecha de inicio, aplicamos el filtro para 'created_at'
        if (!is_null($this->startDate)) {
            $builder->whereDate('created_at', '>=', $this->startDate);
        }

        // Si existe una fecha de fin, aplicamos el filtro para 'created_at'
        if (!is_null($this->endDate)) {
            $builder->whereDate('created_at', '<=', $this->endDate);
        }

        // Si no se pasan fechas de inicio y fin, se filtra por el mismo día
        if (is_null($this->startDate) && is_null($this->endDate)) {
            $builder->whereDate('created_at', '=', now()->format('Y-m-d'));
        }

        // Continuar con el siguiente filtro del pipeline
        return $next($builder);
    }
}

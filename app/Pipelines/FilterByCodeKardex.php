<?php
namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MovementInput;  // Importamos el modelo MovementInput

class FilterByCodeKardex
{
    public function __construct(private $code) {}

    public function __invoke(Builder $builder, Closure $next)
    {
        // Si el código no es nulo, filtramos
        if (!is_null($this->code)) {
            // Usar LOWER para hacer que la comparación sea insensible a mayúsculas/minúsculas
            $builder->whereHas('movementInput', function ($query) {
                $query->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($this->code) . '%']);
            });
        }

        // Continuamos con el siguiente paso del pipeline
        return $next($builder);
    }
}

<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterById {
    public function __construct(private $id) {}

    public function __invoke(Builder $builder, Closure $next) {
        if (!is_null($this->id)) {
            $builder->where('id', 'like', '%' . $this->id . '%');  // Usamos LIKE para búsqueda parcial
        }
        return $next($builder);
    }
}

<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByCode {
    public function __construct(private $code) {}

    public function __invoke(Builder $builder, Closure $next) {
        if (!is_null($this->code)) {
            $builder->where('code', 'like', '%' . $this->code . '%');  // Usamos LIKE para búsqueda parcial
        }
        return $next($builder);
    }
}

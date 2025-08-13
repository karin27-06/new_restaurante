<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByCodigo {
    public function __construct(private $codigo) {}

    public function __invoke(Builder $builder, Closure $next) {
    if (!is_null($this->codigo)) {
        $builder->whereRaw('LOWER(codigo) like ?', ['%' . strtolower($this->codigo) . '%']);
    }
    return $next($builder);
}

}

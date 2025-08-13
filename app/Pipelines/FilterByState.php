<?php

namespace App\Pipelines;
use Closure;
use Illuminate\Database\Eloquent\Builder;
class FilterByState{
    public function __construct(private $state) {}
    public function __invoke(Builder $builder, Closure $next){
        if (!is_null($this->state)) {
            $builder->where('state', (bool) $this->state);
        }
        return $next($builder);
    }
}

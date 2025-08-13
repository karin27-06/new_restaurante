<?php

namespace App\Pipelines;
use Closure;
use Illuminate\Database\Eloquent\Builder;
class FilterByName{
    public function __construct(private ?string $search) {}
    public function __invoke(Builder $builder, Closure $next){
        if (!$this->search) {
            return $next($builder);
        }
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $this->search)));
        $terms = explode(' ', $normalized);
        $builder->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $q->orWhere(function ($sub) use ($term) {
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhereRaw("CASE WHEN state THEN 'activo' ELSE 'inactivo' END ILIKE ?", ["%{$term}%"]);
                });
            }
        });
        return $next($builder);
    }
}

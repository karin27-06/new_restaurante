<?php
namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByNameHistorial
{
    public function handle($query, Closure $next)
    {
        $search = request('search');

        if ($search) {
            $query->whereHas('dish', function (Builder $query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%");  // Usamos ILIKE para PostgreSQL (búsqueda no sensible a mayúsculas)
            });
        }

        return $next($query);
    }
}

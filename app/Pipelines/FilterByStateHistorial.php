<?php
namespace App\Pipelines;

use Closure;

class FilterByStateHistorial
{
    public function handle($query, Closure $next)
    {
        $state = request('state');

        if ($state) {
            $query->where('state', $state);
        }

        return $next($query);
    }
}

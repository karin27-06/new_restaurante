<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterBySupplier
{
    protected $supplier;

    public function __construct($supplier)
    {
        $this->supplier = $supplier;
    }

    public function handle($request, Closure $next)
    {
        if ($this->supplier) {
            $request->whereHas('supplier', function (Builder $query) {
                $query->where('name', 'ILIKE', '%' . $this->supplier . '%');
            });
        }

        return $next($request);
    }
}

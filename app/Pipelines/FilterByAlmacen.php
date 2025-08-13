<?php

namespace App\Pipelines;

use Closure;

class FilterByAlmacen
{
    protected $almacen;

    public function __construct($almacen)
    {
        $this->almacen = $almacen;
    }

    public function handle($request, Closure $next)
    {
        if (!empty($this->almacen)) {
            if (is_numeric($this->almacen)) {
                $request->where('idAlmacen', $this->almacen);
            } else {
                $request->whereHas('almacen', function ($query) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->almacen) . '%']);
                });
            }
        }

        return $next($request);
    }
}

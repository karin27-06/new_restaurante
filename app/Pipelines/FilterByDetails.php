<?php

namespace App\Pipelines;

use Closure;

class FilterByDetails
{
    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle($request, Closure $next)
    {
        if ($this->details) {
            $request->where('details', 'ILIKE', '%' . $this->details . '%');
        }

        return $next($request);
    }
}

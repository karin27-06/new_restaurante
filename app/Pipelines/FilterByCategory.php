<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByCategory
{
    protected $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public function handle($request, Closure $next)
    {
        if ($this->category) {
            $request->whereHas('category', function (Builder $query) {
                $query->where('name', 'ILIKE', '%' . $this->category . '%');
            });
        }

        return $next($request);
    }
}

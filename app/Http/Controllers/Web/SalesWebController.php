<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class SalesWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',Sale::class);
        return Inertia::render('panel/Sale/indexSale');
    }
}

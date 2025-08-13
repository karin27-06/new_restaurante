<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class ProductWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',Product::class);
        return Inertia::render('panel/Product/indexProduct');
    }
}

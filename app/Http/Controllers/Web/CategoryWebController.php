<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class CategoryWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Category::class);
        return Inertia::render('panel/Category/indexCategory');
    }
}

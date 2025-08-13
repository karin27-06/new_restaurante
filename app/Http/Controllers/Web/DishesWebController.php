<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Dishes;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class DishesWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',Dishes::class);
        return Inertia::render('panel/Dishes/dishesindex');
    }
}

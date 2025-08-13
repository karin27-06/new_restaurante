<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Almacen;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class AlmacenWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',Almacen::class);
        return Inertia::render('panel/Almacen/indexAlmacen');
    }
}

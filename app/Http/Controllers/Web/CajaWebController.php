<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class CajaWebController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Caja::class);
        return Inertia::render('panel/Caja/indexCaja');
    }
    public function aperturar(): Response
    {
        return Inertia::render('panel/Caja/AperturarCaja');
    }
    public function cerrar(): Response
    {
        return Inertia::render('panel/Caja/CerrarCaja');
    }


}

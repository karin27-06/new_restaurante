<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\KardexInput;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class MovementInputKardexWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', KardexInput::class);
        return Inertia::render('panel/Kardex/Inputs/indexKardexInput');
    }
}

<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Areas;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class AreasWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',Areas::class);
        return Inertia::render('panel/Areas/indexArea');
    }
}

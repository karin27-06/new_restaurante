<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Floor;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class FloorWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Floor::class);
        return Inertia::render('panel/Floor/indexFloor');
    }
}

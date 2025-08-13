<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class PresentationWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Presentation::class);
        return Inertia::render('panel/Presentation/indexPresentation');
    }
}

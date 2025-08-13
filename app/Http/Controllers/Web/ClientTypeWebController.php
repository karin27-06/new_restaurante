<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\ClientType;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class ClientTypeWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny',ClientType::class);
        return Inertia::render('panel/ClientType/indexClientType');
    }
}

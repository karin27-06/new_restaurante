<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Table;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class TableWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Table::class);
        return Inertia::render('panel/Tables/indexTable');
    }
}

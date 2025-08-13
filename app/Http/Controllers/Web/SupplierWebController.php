<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class SupplierWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Supplier::class);
        return Inertia::render('panel/Supplier/indexSupplier');
    }
}

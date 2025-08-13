<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class OrdersTablesWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Orders::class);
        return Inertia::render('panel/Orders/Mesas/indexMesas');
    }
}

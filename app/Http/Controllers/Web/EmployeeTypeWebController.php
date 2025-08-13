<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class EmployeeTypeWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', EmployeeType::class);
        return Inertia::render('panel/EmployeeType/indexEmployeeType');
    }
}

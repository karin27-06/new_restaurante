<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class EmployeeWebController extends Controller{
    public function index(): Response{
        Gate::authorize('viewAny', Employee::class);
        return Inertia::render('panel/Employee/indexEmployee');
    }
}

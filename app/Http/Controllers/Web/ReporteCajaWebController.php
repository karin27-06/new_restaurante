<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ReporteCaja;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class ReporteCajaWebController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', ReporteCaja::class);
        return Inertia::render('panel/ReporteCaja/indexReporteCaja');
    }

}
<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
class CertificadoWebController extends Controller{
    public function index(): Response{
        return Inertia::render('panel/certificado/indexcertificado');
    }
}

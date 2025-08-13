<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReporteCaja\StoreReporteCajaRequest;
use App\Http\Requests\ReporteCaja\UpdateReporteCajaRequest;
use App\Http\Resources\ReporteCajaResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteCajaExport;
use App\Models\ReporteCaja;
use App\Models\Caja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReporteCajaController extends Controller
{
    // Listar reportes de caja
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ReporteCaja::class);

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        // Carga la relación caja y su vendedor
        $query = ReporteCaja::with(['vendedor', 'caja']);

    // Búsqueda por número de caja o nombre del vendedor
    if ($search) {
        $query->whereHas('vendedor', function($q) use ($search) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
              ->orWhereRaw('LOWER(apellidos) LIKE ?', ['%' . strtolower($search) . '%']);
        })->orWhereHas('caja', function($q) use ($search) {
            $q->where('numero_cajas', 'like', "%$search%");
        });
    }

    return ReporteCajaResource::collection($query->paginate($perPage));
    }

    // Mostrar un reporte específico de caja
    public function show(ReporteCaja $reporteCaja)
    {
        Gate::authorize('view', $reporteCaja);
        return response()->json([
            'state' => true,
            'message' => 'Reporte encontrado',
            'reportecaja' => new ReporteCajaResource($reporteCaja),
        ], 200);
    }
    public function store(StoreReporteCajaRequest $request)
    {
    $validated = $request->validated();

    // Verificar que se envíe un 'id_caja' válido
    if (!isset($validated['id_caja'])) {
        return response()->json([
            'state' => false,
            'message' => 'Falta el ID de la caja.'
        ], 422); // Error de validación si no se envió el 'id_caja'
    }

    // Verificar que la caja con ese ID exista
    $caja = Caja::find($validated['id_caja']);
    if (!$caja) {
        return response()->json([
            'state' => false,
            'message' => 'La caja con el ID proporcionado no existe.'
        ], 404);
    }

    // Crear el reporte de caja con los datos recibidos
    $reporteCaja = ReporteCaja::create([
    'id_caja' => $validated['id_caja'],
    'idVendedor' => $caja ? $caja->idVendedor : null, // <-- Aquí!
    'monto_efectivo' => $validated['monto_efectivo'],
    'monto_tarjeta' => $validated['monto_tarjeta'],
    'monto_yape' => $validated['monto_yape'],
    'monto_transferencia' => $validated['monto_transferencia'],
    'idUsuario' => Auth::id(),
    'fecha_ingreso' => now(),
    'fecha_salida' => now(),
    'estado_caja' => true,
    ]);

    return response()->json([
        'state' => true,
        'message' => 'Reporte de caja creado correctamente',
        'reporte_caja' => new ReporteCajaResource($reporteCaja),
    ]);
}   
    // Actualizar un reporte de caja
    public function update(UpdateReporteCajaRequest $request, ReporteCaja $reporteCaja)
    {
        Gate::authorize('update', $reporteCaja);

        $validated = $request->validated();

        // Actualizar los datos del reporte
        $reporteCaja->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Reporte actualizado correctamente.',
            'reporteCaja' => new ReporteCajaResource($reporteCaja),
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new ReporteCajaExport, 'reporteCajas.xlsx');
    }
}

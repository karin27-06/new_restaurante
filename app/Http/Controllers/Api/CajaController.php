<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cajas\StoreCajaRequest;
use App\Http\Requests\Cajas\UpdateCajaRequest;
use App\Http\Resources\CajaResource;
use App\Models\Caja;
use Illuminate\Http\Request;
use App\Pipelines\FilterByState;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\ReporteCaja;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Caja::class);
        $perPage = $request->input('per_page', 15);
        $state = $request->input('state');
        $search = $request->input('search');
        
        // Pipeline solo para el filtro 'state'
        $query = app(Pipeline::class)
            ->send(Caja::query())
            ->through([
                new FilterByState($state),  // Solo aplicamos el filtro por 'state'
            ])
            ->thenReturn();

        // Búsqueda por el campo 'numero_cajas'
        if ($search) {
            $query->where('numero_cajas', 'like', "%$search%"); // Filtrado por 'numero_cajas'
        }

        return CajaResource::collection($query->paginate($perPage));
    }

    public function store(StoreCajaRequest $request)
    {
    Gate::authorize('create', Caja::class);
    $validated = $request->validated();
    $idUsuario = Auth::user()->id;

    // Obtener el siguiente número disponible para la caja
    $lastCaja = Caja::orderBy('numero_cajas', 'desc')->first();  // Obtener la caja con el mayor número de caja
    $nextCajaNumber = $lastCaja ? $lastCaja->numero_cajas + 1 : 1;  // Si no existe, empezar desde 1

    $cajas = [];
    for ($i = 0; $i < $validated['numero_cajas']; $i++) {
        $cajas[] = Caja::create([
            'state' => true, // Aseguramos que el estado se inicialice como 'Sin ocupar'
            'vendedor' => null, // Sin asignar vendedor
            'idUsuario' => $idUsuario,
            'numero_cajas' => $nextCajaNumber + $i, // Asignar el número secuencial de caja
        ]);

    }
    return response()->json([
        'state' => true,
        'message' => 'Cajas creadas correctamente.',
        'cajas' => $cajas
    ]);
}
    public function show(Caja $caja){
        Gate::authorize('view', $caja);
         // Asegúrate de cargar la relación 'vendedor'
        $caja->load('vendedor');
        return response()->json([
            'state' => true,
            'message' => 'Caja encontrada',
            'caja' => new CajaResource($caja),
        ], 200);
    }
    public function update(UpdateCajaRequest $request, Caja $caja)
{
    Gate::authorize('update', $caja);

    $validated = $request->validated();

    // Verificar si el vendedor está asignado a alguna otra caja, sin importar el estado
    if ($validated['idVendedor']) {

        // Comprobamos si el vendedor ya está asignado a cualquier caja (ocupada o no)
        $existingCaja = Caja::where('idVendedor', $validated['idVendedor'])
                            ->where('id', '!=', $caja->id) // Excluir la caja que estamos editando
                            ->first();

        // Si encontramos que el vendedor ya está asignado a otra caja
        if ($existingCaja) {
            return response()->json([
                'state' => false,
                'message' => 'Este vendedor ya está asignado a otra caja.'  // Aquí devolvemos el mensaje de error
            ], 422);  // Código de estado 422 para errores de validación
        }
    }

    // Si la validación pasa, actualizamos la caja con los datos validados
    $caja->update($validated);

    return response()->json([
        'state' => true,
        'message' => 'Caja actualizada correctamente.',
        'caja' => $caja
    ]);
}

    public function destroy(Caja $caja)
    {
    Gate::authorize('delete', $caja);

    // Verifica si el estado es "Sin ocupar" (true) para permitir la eliminación
    if ($caja->state !== true) {
        return response()->json([
            'state' => false,
            'message' => 'No se puede eliminar una caja ocupada.'
        ]);
    }

    // Eliminar la caja de la base de datos
    $caja->delete();

    return response()->json([
        'state' => true,
        'message' => 'Caja eliminada correctamente.'
    ]);
}
    public function disponibles()
    {
        //Gate::authorize('viewAny', Caja::class);

        return Caja::where('state', true)
            ->select('id', 'numero_cajas', 'state')
            ->orderBy('numero_cajas', 'asc')
            ->get();
    }

    public function aperturar(Request $request)
{
    $caja = Caja::find($request->caja_id);
    $usuario = Auth::user();

    //Gate::authorize('update', [$usuario, $caja]);
    if (!$caja->state) {
        return response()->json([
            'success' => false,
            'message' => 'La caja seleccionada no está disponible'
        ], 422);
    }

    // Ocupar caja
    $caja->update([
        'state' => false,
        'idVendedor' => $usuario->id,
    ]);

    // Crear reporte vacío si NO EXISTE UNO ABIERTO para esa caja
    $reporte = ReporteCaja::where('id_caja', $caja->id)->where('estado_caja', false)->first();
    if (!$reporte) {
        ReporteCaja::create([
            'id_caja' => $caja->id,
            'idVendedor' => $usuario->id,
            'monto_efectivo' => 0.00,
            'monto_tarjeta' => 0.00,
            'monto_yape' => 0.00,
            'monto_transferencia' => 0.00,
            'idUsuario' => $usuario->id,
            'fecha_salida' => null,
            'estado_caja' => false, // Abierta (sin cerrar)
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Caja aperturada correctamente',
        'caja' => new CajaResource($caja),
        'vendedorNombre' => $usuario->name . ' ' . $usuario->apellidos,
    ]);
}
// Método para obtener la caja activa
public function miCajaActiva()
{
    $user = Auth::user();
    $caja = Caja::where('idVendedor', $user->id)
                ->where('state', false) // Caja ocupada
                ->first();

    return response()->json([
        'state' => true,
        'caja' => $caja ? new CajaResource($caja) : null
    ]);
}
public function cerrarCaja(Request $request, $id)
{
    $caja = Caja::findOrFail($id);

    // Buscar el reporte abierto (estado_caja = false)
    $reporte = ReporteCaja::where('id_caja', $caja->id)
        ->where('estado_caja', false)
        ->latest('fecha_ingreso')
        ->first();

    if (!$reporte) {
        return response()->json([
            'state' => false,
            'message' => 'No hay reporte abierto para esta caja.'
        ], 404);
    }

    // Actualizar montos y marcar cierre
    $reporte->update([
        'monto_efectivo'      => $request->monto_efectivo ?? 0,
        'monto_tarjeta'       => $request->monto_tarjeta ?? 0,
        'monto_yape'          => $request->monto_yape ?? 0,
        'monto_transferencia' => $request->monto_transferencia ?? 0,
        'fecha_salida' => now(),
        'estado_caja'         => true, // Cerrada
    ]);

    // Liberar caja
    $caja->update([
        'state' => true,
        'idVendedor' => null,
    ]);

    return response()->json([
        'state' => true,
        'message' => 'Caja cerrada y reporte actualizado correctamente.'
    ]);
}
}

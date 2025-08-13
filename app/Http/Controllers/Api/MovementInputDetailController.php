<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementInputDetails\StoreMovementInputDetailRequest;
use App\Http\Requests\MovementInputDetails\UpdateMovementInputDetailRequest;
use App\Http\Resources\MovementInputDetailResource;
use App\Models\MovementInputDetail;
use App\Pipelines\FilterByNameInput;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Gate;

class MovementInputDetailController extends Controller
{
public function index(Request $request, $id)
{
    Gate::authorize('viewAny', MovementInputDetail::class);

    $perPage = $request->input('per_page', 15);

    // Intentar buscar los detalles con el primer filtro por idMovementInput
    $movementInputDetails = app(Pipeline::class)
        ->send(MovementInputDetail::query()->where('idMovementInput', $id))  // Filtro por idMovementInput
        ->through([new FilterByNameInput($request->input('search'))])
        ->thenReturn()
        ->with('movementInput', 'input')  // Cargar relaciones necesarias
        ->paginate($perPage);  // Pagina los resultados

    // Si no hay detalles, devolver respuesta vacía
    if ($movementInputDetails->isEmpty()) {
        return response()->json([
            'state' => true,
            'message' => 'No hay detalles para este movimiento.',
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'total' => 0,
                'last_page' => 1,
                'per_page' => $perPage,
            ],
            'subtotal' => 0,
            'total_igv' => 0,
            'total' => 0,
        ]);
    }

    // Si hay detalles, proceder con los cálculos de totales
    $subtotal = 0;
    $totalIGV = 0;
    $total = 0;

    foreach ($movementInputDetails as $detail) {
        // Lógica para calcular subtotal, IGV y total
        if ($detail->movementInput->igv_state == 0) {
            $igv = $detail->totalPrice * 0.18;
            $subtotal += $detail->totalPrice - $igv;
            $totalIGV += $igv;
        } else if ($detail->movementInput->igv_state == 1) {
            $igv = $detail->totalPrice * 0.18;
            $totalIGV += $igv;
            $subtotal += $detail->totalPrice;
        }
    }

    // Total final
    $total = $subtotal + $totalIGV;

    return response()->json([
        'state' => true,
        'message' => 'Detalles de movimiento cargados correctamente.',
        'data' => MovementInputDetailResource::collection($movementInputDetails),
        'meta' => [
            'current_page' => $movementInputDetails->currentPage(),
            'total' => $movementInputDetails->total(),
            'last_page' => $movementInputDetails->lastPage(),
            'per_page' => $movementInputDetails->perPage(),
        ],
        'subtotal' => number_format($subtotal, 2),
        'total_igv' => number_format($totalIGV, 2),
        'total' => number_format($total, 2),
    ]);
}






public function store(StoreMovementInputDetailRequest $request)
{
    Gate::authorize('create', MovementInputDetail::class);

    $validated = $request->validated(); // Valida los datos según la solicitud

    // Verificar si ya existe un detalle de movimiento con el mismo idInput e idMovementInput
    $exists = MovementInputDetail::where('idMovementInput', $validated['idMovementInput'])
                                ->where('idInput', $validated['idInput'])
                                ->exists();

    if ($exists) {
        return response()->json([
            'state' => false,
            'message' => 'Ya existe un detalle de movimiento con el mismo insumo para este movimiento.',
        ], 422); // 422 Unprocessable Entity si el registro ya existe
    }

    // Crear un nuevo detalle de movimiento
    $movementInputDetail = MovementInputDetail::create($validated);

    return response()->json([
        'state' => true,
        'message' => 'Detalle de movimiento registrado correctamente.',
        'movementInputDetail' => new MovementInputDetailResource($movementInputDetail)
    ]);
}



    public function show(MovementInputDetail $movementInputDetail)
    {
        Gate::authorize('view', $movementInputDetail);

        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento encontrado',
            'movementInputDetail' => new MovementInputDetailResource($movementInputDetail),
        ], 200);
    }


    public function update(UpdateMovementInputDetailRequest $request, MovementInputDetail $MovementInputDetail)
    {
        Gate::authorize('update', $MovementInputDetail);

        $validated = $request->validated();

        // Check if the MovementInput exists
        $movementInputExists = \App\Models\MovementInput::find($validated['idMovementInput']);

        if (!$movementInputExists) {
            return response()->json([
                'state' => false,
                'message' => 'El movimiento de insumo no existe.',
            ], 404);  // Return error if the referenced movement doesn't exist
        }

        // Check if this movement input and input combination already exists
        $inputExists = MovementInputDetail::where('idMovementInput', $validated['idMovementInput'])
            ->where('idInput', $validated['idInput'])
            ->where('id', '!=', $MovementInputDetail->id)  // Verify for other records with different ID
            ->exists();

        if ($inputExists) {
            return response()->json([
                'errors' => ['input' => ['Ya existe este detalle para el mismo movimiento.']]
            ], 422);
        }

        // Update the Movement Input Detail
        $MovementInputDetail->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento actualizado de manera correcta',
        ]);
    }

    public function destroy($id)
    {
        // Buscar el detalle de movimiento por su ID
        $movementInputDetail = MovementInputDetail::find($id);

        // Verificar si el detalle de movimiento existe
        if (!$movementInputDetail) {
            return response()->json([
                'state' => false,
                'message' => 'Detalle de movimiento no encontrado.',
            ], 404);
        }

        // Eliminar el detalle de movimiento
        $movementInputDetail->delete();

        // Retornar una respuesta exitosa
        return response()->json([
            'state' => true,
            'message' => 'Detalle de movimiento eliminado correctamente.',
        ]);
    }
}

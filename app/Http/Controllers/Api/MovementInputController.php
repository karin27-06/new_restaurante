<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementInputs\StoreMovementInputRequest;
use App\Http\Requests\MovementInputs\UpdateMovementInputRequest;
use App\Http\Resources\MovementInputResource;
use App\Models\MovementInput;
use App\Pipelines\FilterByCode;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class MovementInputController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MovementInput::class);

        $perPage = $request->input('per_page', 15);

        $movementsInput = app(Pipeline::class)
            ->send(MovementInput::query())
            ->through(pipes: [
                new FilterByCode($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn()
            ->paginate($perPage);

        return MovementInputResource::collection($movementsInput);
    }

    public function store(StoreMovementInputRequest $request)
    {
        Gate::authorize('create', MovementInput::class);

        $validated = $request->validated();

        $exists = MovementInput::whereRaw('LOWER(code) = ?', [strtolower($validated['code'])])->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['code' => ['Este código ya está registrado.']]
            ], 422);
        }

        $movementInput = MovementInput::create($validated);

        return response()->json([
            'state' => true,
            'message' => 'Movimiento registrado correctamente.',
            'movement' => $movementInput
        ]);
    }

    public function show(MovementInput $movementInput)
    {
        Gate::authorize('view', $movementInput);

        return response()->json([
            'state' => true,
            'message' => 'Movimiento encontrado',
            'movement' => new MovementInputResource($movementInput),
        ], 200);
    }

    public function update(UpdateMovementInputRequest $request, MovementInput $movementInput)
    {
        Gate::authorize('update', arguments: $movementInput);

        $validated = $request->validated();

        $codeExists = MovementInput::whereRaw('LOWER(code) = ?', [strtolower($validated['code'])])
            ->where('id', '!=', $movementInput->id)
            ->exists();

        if ($codeExists) {
            return response()->json([
                'errors' => ['code' => ['Este código ya está registrado.']]
            ], 422);
        }

        $movementInput->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Movimiento actualizado de manera correcta',
            'movement' => new MovementInputResource($movementInput->refresh()),
        ]);
    }

 public function destroy(MovementInput $movementInput)
{
    Gate::authorize('delete', $movementInput);

    // Eliminar los registros relacionados en la tabla detail_movements_inputs
    $movementInput->detailMovements()->delete();  // Esto eliminará todos los registros relacionados
    
    $movementInput->kardexInputs()->delete();  // Esto eliminará todos los registros relacionados

    // Ahora puedes eliminar el movimiento en movementsInput
    $movementInput->delete();

    return response()->json([
        'state' => true,
        'message' => 'Movimiento eliminado correctamente',
    ]);
}


 

    
}

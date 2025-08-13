<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\DishesExport;
use App\Imports\DishImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Dishes\StoreDishesRequests;
use App\Http\Requests\Dishes\UpdateDishesRequests;
use App\Http\Resources\DishesResource;
use App\Models\Dishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterById;
use App\Pipelines\FilterByState;
use Illuminate\Pipeline\Pipeline;
class DishesController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Dishes::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $id = $request->input(key: 'id');
        $state = $request->input('state');
        $query = app(Pipeline::class)
            ->send(Dishes::query())
            ->through([
                new FilterByName($search),
                new FilterByState($state),
                new FilterById($id), 

            ])
            ->thenReturn();

        return DishesResource::collection($query->paginate($perPage));
    }
    public function store(StoreDishesRequests $request)
{
    Gate::authorize('create', Dishes::class);
    $validated = $request->validated();

    // Verificar si el plato con el mismo nombre ya existe (sin importar mayúsculas y minúsculas)
    $exists = Dishes::whereRaw('LOWER(name) = ?', [$validated['name']])->exists();
    if ($exists) {
        return response()->json([
            'errors' => ['name' => ['Este nombre ya está registrado.']]
        ], 422);
    }

    // Crear el plato
    $dishes = Dishes::create($validated);

    // Verificar si se enviaron insumos (inputs)
    if ($request->has('insumos') && !empty($request->insumos)) {
        // Asociar los insumos seleccionados al plato
        $dishes->insumos()->sync($request->insumos); // `sync` mantiene la relación muchos a muchos
    }

    return response()->json([
        'state' => true,
        'message' => 'Plato registrado correctamente.',
        'dishes' => $dishes
    ]);
}

    public function show(Dishes $dishes){
        Gate::authorize('view', $dishes);
        return response()->json([
            'state' => true,
            'message' => 'Plato encontrado',
            'dishes' => new DishesResource($dishes),
        ], 200);
    }
public function update(UpdateDishesRequests $request, Dishes $dishes)
{
    Gate::authorize('update', $dishes);

    $validated = $request->validated();

    // Verificar si el plato con el mismo nombre ya existe (sin importar mayúsculas y minúsculas)
    $exists = Dishes::whereRaw('LOWER(name) = ?', [$validated['name']])
        ->where('id', '!=', $dishes->id)
        ->exists();

    if ($exists) {
        return response()->json([
            'errors' => ['name' => ['Este nombre ya está registrado.']]
        ], 422);
    }

    // Actualizamos el plato
    $dishes->update($validated);

    // Si los insumos fueron proporcionados, actualizamos la relación
    if ($request->has('insumos') && !empty($request->insumos)) {
        $dishes->insumos()->sync($request->insumos); // Sincroniza los insumos seleccionados
    }

    return response()->json([
        'state' => true,
        'message' => 'Plato actualizado correctamente',
        'dishes' => new DishesResource($dishes->refresh())
    ]);
}

    public function destroy(Dishes $dishes){
        Gate::authorize('delete', $dishes);
        $dishes->delete();
        return response()->json([
            'state' => true,
            'message' => 'Platos eliminado de manera correcta',
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new DishesExport, 'Platos.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new DishImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los platos realizado correctamente.'
        ]);
    }
}

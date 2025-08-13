<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\AlmacenesExport;
use App\Imports\AlmacenImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Almacen\StoreAlmacenRequest;
use App\Http\Requests\Almacen\UpdateAlmacenRequest;
use App\Models\Almacen;
use App\Http\Resources\AlmacenResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Pipeline\Pipeline;
class AlmacenController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Almacen::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $query = app(Pipeline::class)
            ->send(Almacen::query())
            ->through([
                new FilterByName($search),
                new FilterByState($state),
            ])
            ->thenReturn();

        return AlmacenResource::collection($query->paginate($perPage));
    }
    public function store(StoreAlmacenRequest $request){
        Gate::authorize('create', Almacen::class);
        $validated = $request->validated();
        $exists = Almacen::whereRaw('LOWER(name) = ?', [$validated['name']])->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $almacen = Almacen::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén registrado correctamente.',
            'almacen' => $almacen
        ]);
    }
    public function show(Almacen $almacen){
        Gate::authorize('view', $almacen);
        return response()->json([
            'state' => true,
            'message' => 'Almacen encontrado',
            'almacen' => new AlmacenResource($almacen),
        ], 200);
    }
    public function update(UpdateAlmacenRequest $request, Almacen $almacen){
        Gate::authorize('update', $almacen);
        $validated = $request->validated();
        $exists = Almacen::whereRaw('LOWER(name) = ?', [$validated['name']])
            ->where('id', '!=', $almacen->id)
            ->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $almacen->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén actualizado correctamente.',
            'almacen' => $almacen->refresh()
        ]);
    }
    public function destroy(Almacen $almacen)
    {
    Gate::authorize('delete', $almacen);

    if ($almacen->tieneRelaciones()) {
        return response()->json([
            'state' => false,
            'message' => 'No se puede eliminar este almacén porque está relacionado con otros registros.'
        ], 400);
    }

    $almacen->delete();

    return response()->json([
        'state' => true,
        'message' => 'Almacén eliminado correctamente',
    ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new AlmacenesExport, 'Almacenes.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new AlmacenImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los almacenes realizada correctamente.'
        ]);
    }
}

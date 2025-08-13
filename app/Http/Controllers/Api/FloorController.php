<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\FloorsExport;
use App\Imports\FloorImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Pisos\StoreFloorRequest;
use App\Http\Requests\Pisos\UpdateFloorRequest;
use App\Http\Resources\FloorResource;
use App\Models\Floor;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class FloorController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Floor::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $query = app(Pipeline::class)
            ->send(Floor::query())
            ->through([
                new FilterByName($search),
                new FilterByState($state),
            ])
            ->thenReturn();

        return FloorResource::collection($query->paginate($perPage));
    }
    public function store(StoreFloorRequest $request){
        Gate::authorize('create', Floor::class);
        $validated = $request->validated();
        $floor = Floor::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Piso registrado correctamente.',
            'floor' => $floor
        ]);
    }
    public function show(Floor $floor){
        Gate::authorize('view', $floor);
        return response()->json([
            'state' => true,
            'message' => 'Piso encontrado',
            'floor' => new FloorResource($floor),
        ], 200);
    }
    public function update(UpdateFloorRequest $request, Floor $floor){
        Gate::authorize('update', $floor);
        $validated = $request->validated();
        $floor->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Piso actualizado correctamente.',
            'floor' => $floor->refresh()
        ]);
    }
    public function destroy(Floor $floor){
        Gate::authorize('delete', $floor);
        if($floor->tieneRelaciones())
        {
            return response()->json([
                'state'=> false,
                'message' => 'No se puede eliminar el piso porque tiene relaciones con otros registros'
            ],400);
        }
        $floor->delete();
        return response()->json([
            'state' => true,
            'message' => 'Piso eliminado correctamente',
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new FloorsExport, 'Pisos.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new FloorImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los pisos realizado correctamente.'
        ]);
    }
}

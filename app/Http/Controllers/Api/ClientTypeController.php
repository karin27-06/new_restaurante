<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\ClientTypesExport;
use App\Imports\ClientTypeImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ClientType;
use App\Http\Requests\Tipo_Cliente\StoreClientTypeRequest;
use App\Http\Requests\Tipo_Cliente\UpdateClientTypeRequest;
use App\Http\Resources\ClientTypeResource;
use App\Pipelines\FilterByName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pipelines\FilterByState;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
class ClientTypeController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', ClientType::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $query = app(Pipeline::class)
            ->send(ClientType::query())
            ->through([
                new FilterByName($search),
                new FilterByState($state),
            ])
            ->thenReturn();

        return ClientTypeResource::collection($query->paginate($perPage));
    }
    public function store(StoreClientTypeRequest $request){
        Gate::authorize('create', ClientType::class);
        $validated = $request->validated();
        if (ClientType::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])->exists()) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $tipoCliente = ClientType::create($validated);
        return response()->json([
            'message' => 'Tipo de cliente creado con éxito',
            'clientType' => $tipoCliente,
        ]);
    }
    public function show(ClientType $clientType){
        Gate::authorize('view', $clientType);
        return response()->json([
            'state' => true,
            'message' => 'Tipo de cliente encontrado',
            'clientType' => new ClientTypeResource($clientType),
        ], 200);
    }
    public function update(UpdateClientTypeRequest $request, ClientType $clientType){
        Gate::authorize('update', $clientType);
        $validated = $request->validated();
        $nameExists = ClientType::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $clientType->id)
            ->exists();
        if ($nameExists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $clientType->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Tipo de cliente actualizado de manera correcta',
            'clientType' => new ClientTypeResource($clientType->refresh()),
        ]);
    }
public function destroy(ClientType $clientType)
{
    // Restablecer el contador del ID después de la eliminación en PostgreSQL
    DB::statement('ALTER SEQUENCE client_types_id_seq RESTART WITH 1');
    
    Gate::authorize('delete', $clientType);
    if ($clientType->tieneRelaciones()) {
        return response()->json([
            'state' => false,
            'message' => 'No se puede eliminar este tipo de cliente porque está relacionado con otros registros.'
        ], 400);
    }
    $clientType->delete();

    return response()->json([
        'state' => true,
        'message' => 'Tipo de cliente eliminado de manera correcta',
    ]);
}
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new ClientTypesExport, 'Tipo_cientes.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new ClientTypeImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los tipos de cliente realizada correctamente.'
        ]);
    }
}
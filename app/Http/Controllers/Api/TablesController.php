<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\TablesExport;
use App\Imports\TableImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use App\Pipelines\FilterByNumTable;
use App\Pipelines\FilterByState;

class TablesController extends Controller
{
public function index(Request $request)
{
    Gate::authorize('viewAny', Table::class);

    $perPage = $request->input('per_page', 15);

    // Obtenemos los filtros de idArea e idFloor
    $idArea = $request->input('idArea');
    $idFloor = $request->input('idFloor');

    $tables = app(Pipeline::class)
        ->send(Table::query())
        ->through([
            new FilterByNumTable($request->input('search')),
            new FilterByState($request->input('state')),
        ])
        ->thenReturn();

    // Aplicamos el filtro por idArea si existe
    if ($idArea) {
        $tables->where('idArea', $idArea);
    }

    // Aplicamos el filtro por idFloor si existe
    if ($idFloor) {
        $tables->where('idFloor', $idFloor);
    }

    $tables = $tables->paginate($perPage);

    return TableResource::collection($tables);
}


    public function store(StoreTableRequest $request)
    {
        Gate::authorize('create', Table::class);

        $validated = $request->validated();

        $exists = Table::whereRaw('LOWER(name) = ?', [$validated['name']])
            ->where('tablenum', $validated['tablenum'])
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Ya existe una mesa con este nombre y número.']]
            ], 422);
        }

        $table = Table::create($validated);

        return response()->json([
            'state' => true,
            'message' => 'Mesa registrada correctamente.',
            'table' => new TableResource($table),
        ]);
    }

    public function show(Table $table)
    {
        Gate::authorize('view', $table);

        return response()->json([
            'state' => true,
            'message' => 'Mesa encontrada.',
            'table' => new TableResource($table),
        ], 200);
    }

    public function update(UpdateTableRequest $request, Table $table)
{
    Gate::authorize('update', $table);

    $validated = $request->validated();

    // Solo verificar nombre si se modificó
    if (strtolower($validated['name']) !== strtolower($table->name)) {
        $existsName = Table::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $table->id)
            ->exists();

        if ($existsName) {
            return response()->json([
                'errors' => ['name' => ['Ya existe una mesa con este nombre.']]
            ], 422);
        }
    }

    // Solo verificar número si se modificó
    if ($validated['tablenum'] !== $table->tablenum) {
        $existsNumber = Table::where('tablenum', $validated['tablenum'])
            ->where('id', '!=', $table->id)
            ->exists();

        if ($existsNumber) {
            return response()->json([
                'errors' => ['tablenum' => ['Ya existe una mesa con este número.']]
            ], 422);
        }
    }

    $table->update($validated);

    return response()->json([
        'state' => true,
        'message' => 'Mesa actualizada correctamente.',
        'table' => $table->refresh()
    ]);
}
 public function destroy(Table $table){
        Gate::authorize('delete', $table);
        $table->delete();
        return response()->json([
            'state' => true,
            'message' => 'Mesa eliminada de manera correcta',
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new TablesExport, 'Mesas.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new TableImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de las mesas realizado correctamente.'
        ]);
    }
}

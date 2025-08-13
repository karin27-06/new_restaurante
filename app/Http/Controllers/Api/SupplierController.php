<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\SuppliersExport;
use App\Imports\SupplierImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Proveedor\StoreSupplierRequest;
use App\Http\Requests\Proveedor\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SupplierController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Supplier::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $query = app(Pipeline::class)
            ->send(Supplier::query())
            ->through([
                new FilterByName($search),
                new FilterByState($request->input('state')), //sttate
            ])
            ->thenReturn();

        return SupplierResource::collection($query->paginate($perPage));
    }
    public function store(StoreSupplierRequest $request){
        Gate::authorize('create', Supplier::class);
        $validated = $request->validated();
        $supplier = Supplier::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Proveedor registrado correctamente.',
            'supplier' => $supplier
        ]);
    }
    public function show(Supplier $supplier){
        Gate::authorize('view', $supplier);
        return response()->json([
            'state' => true,
            'message' => 'Proveedor encontrado',
            'supplier' => new SupplierResource($supplier)
        ]);
    }
    public function update(UpdateSupplierRequest $request, Supplier $supplier){
        Gate::authorize('update', $supplier);
        $validated = $request->validated();
        $supplier->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Proveedor actualizado correctamente.',
            'supplier' => $supplier->refresh()
        ]);
    }
    public function destroy(Supplier $supplier){
        Gate::authorize('delete', $supplier);

    if (
        $supplier->tieneRelaciones()
        // Otras relaciones si existen
    ) {
        return response()->json([
            'state' => false,
            'message' => 'No se puede eliminar este proveedor porque está relacionada con otros registros.'
        ], 400);
    }
        $supplier->delete();
        return response()->json([
            'state' => true,
            'message' => 'Proveedor eliminado correctamente'
        ]);
    }

    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new SuppliersExport, 'Proveedores.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new SupplierImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los proveedores realizada correctamente.'
        ]);
    }
}

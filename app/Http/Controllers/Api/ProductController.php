<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\ProductsExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Productos\StoreProductRequest;
use App\Http\Requests\Productos\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Pipelines\FilterByAlmacen;
use App\Pipelines\FilterByCategory;
use App\Pipelines\FilterByDetails;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Product::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $category = $request->input('category');
        $details = $request->input('details');
        $almacen = $request->input('almacen');

        $query = app(Pipeline::class)
            ->send(Product::query()->with(['category', 'almacen']))
            ->through([
                new FilterByName($search),
                new FilterByState($state),
                new FilterByCategory($category),
                new FilterByDetails($details),
                new FilterByAlmacen($almacen),
            ])
            ->thenReturn();

        return ProductResource::collection($query->paginate($perPage));
    }
    public function store(StoreProductRequest $request){
        Gate::authorize('create', Product::class);
        $validated = $request->validated();
        $product = Product::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Producto registrado correctamente.',
            'product' => $product
        ]);
    }
    public function show(Product $product){
        Gate::authorize('view', $product);
        return response()->json([
            'state' => true,
            'message' => 'Producto encontrado',
            'product' => new ProductResource($product),
        ], 200);
    }
    public function update(UpdateProductRequest $request, Product $product){
        Gate::authorize('update', $product);
        $validated = $request->validated();
        $product->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Producto actualizado correctamente.',
            'product' => $product->refresh()
        ]);
    }
    public function destroy(Product $product){
        Gate::authorize('delete', $product);
        $product->delete();
        return response()->json([
            'state' => true,
            'message' => 'Producto eliminado correctamente',
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'Productos.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new ProductImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los productos realizado correctamente.'
        ]);
    }   
}

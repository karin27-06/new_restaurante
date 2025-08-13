<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\CategoriesExport;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Categoria\StoreCategoryRequest;
use App\Http\Requests\Categoria\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Category::class);
        $perPage = $request->input('per_page', 15);
        $categories = app(Pipeline::class)
            ->send(Category::query())
            ->through([
                new FilterByName($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn()
            ->paginate($perPage);
        return CategoryResource::collection($categories);
    }
    public function store(StoreCategoryRequest $request){
        Gate::authorize('create', Category::class);
        $validated = $request->validated();
        $exists = Category::whereRaw('LOWER(name) = ?', [$validated['name']])->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $category = Category::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén registrado correctamente.',
            'category' => $category
        ]);
    }
    public function show(Category $category){
        Gate::authorize('view', $category);
        return response()->json([
            'state' => true,
            'message' => 'Categoría encontrada',
            'category' => new CategoryResource($category),
        ], 200);
    }
    public function update(UpdateCategoryRequest $request, Category $category){
        Gate::authorize('update', $category);
        $validated = $request->validated();
        $nameExists = Category::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $category->id)
            ->exists();
        if ($nameExists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $category->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Tipo de cliente actualizado de manera correcta',
            'category' => new CategoryResource($category->refresh()),
        ]);
    }
    public function destroy(Category $category){
    Gate::authorize('delete', $category);

    if (
        $category->tieneRelaciones()
    ) {
        return response()->json([
            'state' => false,
            'message' => 'No se puede eliminar esta categoría porque está relacionada con otros registros.'
        ], 400);
    }

    $category->delete();

    return response()->json([
        'state' => true,
        'message' => 'Categoría eliminada correctamente',
    ]);
}
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new CategoriesExport, 'Categorías.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new CategoryImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de las categorias realizada correctamente.'
        ]);
    }
}

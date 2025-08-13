<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\PresentationsExport;
use App\Imports\PresentationImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Presentacion\StorePresentationRequest;
use App\Http\Requests\Presentacion\UpdatePresentationRequest;
use App\Http\Resources\PresentationResource;
use App\Models\Presentation;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PresentationController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Presentation::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $query = app(Pipeline::class)
            ->send(Presentation::query())
            ->through([
                new FilterByName($search),
                new FilterByState($request->input('state')), //state
            ])
            ->thenReturn();

        return PresentationResource::collection($query->paginate($perPage));
    }
    public function store(StorePresentationRequest $request){
        Gate::authorize('create', Presentation::class);
        $validated = $request->validated();
        $presentation = Presentation::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Presentación registrada correctamente.',
            'presentation' => $presentation
        ]);
    }
    public function show(Presentation $presentation){
        Gate::authorize('view', $presentation);
        return response()->json([
            'state' => true,
            'message' => 'Presentación encontrada',
            'presentation' => new PresentationResource($presentation)
        ]);
    }
    public function update(UpdatePresentationRequest $request, Presentation $presentation){
        Gate::authorize('update', $presentation);
        $validated = $request->validated();
        $presentation->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Presentación actualizada correctamente.',
            'presentation' => $presentation->refresh()
        ]);
    }
    public function destroy(Presentation $presentation){
        Gate::authorize('delete', $presentation);
        $presentation->delete();
        return response()->json([
            'state' => true,
            'message' => 'Presentación eliminada correctamente'
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new PresentationsExport, 'Presentaciones.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new PresentationImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de las presentaciones realizada correctamente.'
        ]);
    }
}

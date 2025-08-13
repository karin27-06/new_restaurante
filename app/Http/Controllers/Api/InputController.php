<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\InputsExport;
use App\Imports\InputImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Inputs\StoreInputRequest;
use App\Http\Requests\Inputs\UpdateInputRequest;
use App\Http\Resources\InputResource;
use App\Models\Input;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class InputController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Input::class);

        $perPage = $request->input('per_page', 15);

        $inputs = app(Pipeline::class)
            ->send(Input::query())
            ->through([
                new FilterByName($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn()
            ->paginate($perPage);

        return InputResource::collection($inputs);
    }

    public function store(StoreInputRequest $request)
    {
        Gate::authorize('create', Input::class);

        $validated = $request->validated();

        $exists = Input::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }

        $input = Input::create($validated);

        return response()->json([
            'state' => true,
            'message' => 'Insumo registrado correctamente.',
            'input' => $input
        ]);
    }

    public function show(Input $input)
    {
        Gate::authorize('view', $input);

        return response()->json([
            'state' => true,
            'message' => 'Insumo encontrado',
            'input' => new InputResource($input),
        ], 200);
    }

    public function update(UpdateInputRequest $request, Input $input)
    {
        Gate::authorize('update', $input);

        $validated = $request->validated();

        $nameExists = Input::whereRaw('LOWER(name) = ?', [strtolower($validated['name'])])
            ->where('id', '!=', $input->id)
            ->exists();

        if ($nameExists) {
            return response()->json([
                'errors' => ['name' => ['Este nombre ya está registrado.']]
            ], 422);
        }

        $input->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Insumo actualizado de manera correcta',
            'input' => new InputResource($input->refresh()),
        ]);
    }

    public function destroy(Input $input)
    {
        Gate::authorize('delete', $input);

        $input->delete();

        return response()->json([
            'state' => true,
            'message' => 'Insumo eliminado correctamente',
        ]);
    }
    #EXPORTACION
    public function exportExcel()
    {
        return Excel::download(new InputsExport, 'Insumos.xlsx');
    }

    #IMPORTACION
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);
    
        Excel::import(new InputImport, $request->file('archivo'));
    
        return response()->json([
            'message' => 'Importación de los insumos realizado correctamente.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Input;
use App\Models\KardexInput;
use App\Models\MovementInputDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputStockController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los insumos que tengan stock
        $inputsWithStock = Input::select('inputs.id', 'inputs.name', 'inputs.quantityUnitMeasure', 'inputs.unitMeasure')
            ->leftJoin('detail_movements_inputs', 'inputs.id', '=', 'detail_movements_inputs.idInput')
            ->leftJoin('kardex_inputs', 'detail_movements_inputs.idMovementInput', '=', 'kardex_inputs.idMovementInput')
            ->selectRaw('SUM(CASE WHEN kardex_inputs.movement_type = 0 THEN detail_movements_inputs.quantity
                        WHEN kardex_inputs.movement_type = 1 THEN -detail_movements_inputs.quantity
                        ELSE 0 END) AS stock')
            ->groupBy('inputs.id', 'inputs.name', 'inputs.quantityUnitMeasure', 'inputs.unitMeasure')
            ->havingRaw('SUM(CASE WHEN kardex_inputs.movement_type = 0 THEN detail_movements_inputs.quantity
                        WHEN kardex_inputs.movement_type = 1 THEN -detail_movements_inputs.quantity
                        ELSE 0 END) > 0')
            ->get();

        // Devolver los datos en la respuesta
        return response()->json([
            'state' => true,
            'message' => 'Lista de insumos con stock disponible',
            'inputs' => $inputsWithStock
        ]);
    }
}

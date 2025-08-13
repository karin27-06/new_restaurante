<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDishResource;

use App\Models\OrderDishes;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pipeline\Pipeline;
use App\Pipelines\FilterByNameHistorial;
use App\Pipelines\FilterByStateHistorial;
use App\Http\Requests\OrderDish\UpdateOrdersDishesRequest;
use App\Models\Dishes;

class OrderDishController extends Controller
{
    // Lista de pedidos con relaciones
public function index(Request $request)
{
    Gate::authorize('viewAny', arguments: Orders::class);

    $perPage = $request->input('per_page', 15);
    $idOrder = $request->input('idOrder');


    $query = OrderDishes::query();

    // Filtro condicional por idOrder
    if (!is_null($idOrder)) {
        $query->where('idOrder', $idOrder);
    }

    // Aplica otros filtros si existen
    $query = app(Pipeline::class)
        ->send($query)
        ->through([
            new FilterByNameHistorial($request->input('search')),
            new FilterByStateHistorial($request->input(key: 'state')),
        ])
        ->thenReturn();

    $orders = $query->paginate($perPage);

    return OrderDishResource::collection($orders);
}
public function update(UpdateOrdersDishesRequest $request, $id)
{
    $orderDish = OrderDishes::findOrFail($id);

    // Obtener el nuevo estado de la solicitud
    $newState = $request->input('state');

    // Validar las transiciones de estado
    if ($orderDish->state === 'pendiente') {
        if ($newState === 'cancelado') {
            // Si el estado es pendiente y se solicita "cancelar", cambiar el estado a "cancelado"
            $orderDish->state = 'cancelado';

            // Aumentar la cantidad del plato en la tabla Dishes
            $dish = Dishes::find($orderDish->idDishes);
            if ($dish) {
                $dish->quantity += $orderDish->quantity;  // Incrementar la cantidad
                $dish->save();
            }

            // Guardar el cambio en el estado del platillo
            $orderDish->save();

            return response()->json([
                'message' => 'Platillo cancelado correctamente.',
                'data' => new OrderDishResource($orderDish)
            ], 200);
        }

        if ($newState === 'en preparación') {
            // Si el estado es pendiente y se solicita "en preparación", cambiar el estado a "en preparación"
            $orderDish->state = 'en preparación';
            $orderDish->save();

            return response()->json([
                'message' => 'Platillo marcado como en preparación.',
                'data' => new OrderDishResource($orderDish)
            ], 200);
        }
    }

    if ($orderDish->state === 'en preparación') {
        if ($newState === 'en entrega') {
            // Si el estado es "en preparación" y se solicita "en entrega", cambiar el estado a "en entrega"
            $orderDish->state = 'en entrega';
            $orderDish->save();

            return response()->json([
                'message' => 'Platillo marcado como en entrega.',
                'data' => new OrderDishResource($orderDish)
            ], 200);
        }
    }

    if ($orderDish->state === 'en entrega') {
        if ($newState === 'completado') {
            // Si el estado es "en entrega" y se solicita "completado", cambiar el estado a "completado"
            $orderDish->state = 'completado';
            $orderDish->save();

            return response()->json([
                'message' => 'Platillo marcado como completado.',
                'data' => new OrderDishResource($orderDish)
            ], 200);
        }
    }

    return response()->json([
        'message' => 'Transición de estado no permitida.',
    ], 400);
}


}

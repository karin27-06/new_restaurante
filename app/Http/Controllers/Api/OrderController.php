<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrdersRequest;
use App\Http\Requests\Orders\UpdateOrdersRequest;
use App\Http\Resources\OrderResource;
use App\Models\OrderDishes;
use App\Models\Orders;
use App\Models\Dishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Lista de pedidos con relaciones
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Orders::class);

        $perPage = $request->input('per_page', 15);
        $mesaId = $request->query('mesa_id');
    $idOrderDish = $request->query('id'); // Filtro por el id de orderDishes
    $query = Orders::with(['customer', 'table', 'user', 'orderDishes'])
                ->orderBy('created_at', 'desc');
           
    if ($mesaId) {
        $query->where('idTable', $mesaId)
              ->where('state', '!=', 'finalizado');
    }

    // Filtro por id de orderDishes
    if ($idOrderDish) {
        $query->whereHas('orderDishes', function ($q) use ($idOrderDish) {
            $q->where('id', $idOrderDish);
        });
    }
        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }

    // Registrar pedido con detalles (platos)
public function store(StoreOrdersRequest $request)
{
    Gate::authorize('create', Orders::class);

    $validated = $request->validated();
    $platos = $validated['platos'];
    unset($validated['platos']); // Quita 'platos' para no insertarlo directamente

    DB::beginTransaction();
    try {
        // 1. Buscar si ya existe una orden activa (no finalizada) en esa mesa
        $order = Orders::where('idTable', $validated['idTable'])
            ->where('state', '!=', 'finalizado')
            ->latest()
            ->first();

        // 2. Si no existe, la creamos
        if (!$order) {
            $order = Orders::create($validated);
        }

        // 3. Agregamos los platos a order_dishes y acumulamos el total
        $totalExtra = 0;

        foreach ($platos as $plato) {
            OrderDishes::create([
                'idOrder'   => $order->id,
                'idDishes'  => $plato['id'],
                'quantity'  => $plato['cantidad'],
                'price'     => $plato['price'],
            ]);



             // 3.2 Actualizar el quantity de dishes restando la cantidad pedida
            $dish = Dishes::find($plato['id']);
            if ($dish) {
                $dish->quantity -= $plato['cantidad'];  // Resta la cantidad pedida
                $dish->save();  // Guarda los cambios
            }

            $totalExtra += $plato['cantidad'] * $plato['price'];
        }



        $order->save();

        DB::commit();

        return response()->json([
            'message' => 'Pedido agregado correctamente.',
            'order' => new OrderResource($order->load(['customer', 'table', 'user', 'orderDishes']))
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error al registrar el pedido.',
            'error' => $e->getMessage()
        ], 500);
    }
}



    // Ver pedido específico
    public function show(Orders $order)
    {
        Gate::authorize('view', $order);
        $order->load(['customer', 'table', 'user', 'orderDishes']);

        return new OrderResource($order);
    }

      // Actualizar solo la información de la orden
    public function update(UpdateOrdersRequest $request, Orders $order)
    {
        Gate::authorize('update', $order);

        // Validar los datos del request
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. Actualizar solo la información de la orden
            $order->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Pedido actualizado correctamente.',
                'order' => new OrderResource($order->load(['customer', 'table', 'user']))
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar pedido
    public function destroy(Orders $order)
    {
        Gate::authorize('delete', $order);

        $order->delete();

        return response()->json([
            'message' => 'Orden eliminada correctamente.'
        ]);
    }

    
}

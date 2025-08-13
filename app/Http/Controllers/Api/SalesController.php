<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Models\Orders;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SalesController extends Controller
{
    // Mostrar todas las ventas con paginación y filtros
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Sale::class);

        $perPage = $request->input('per_page', 15);
        $sales = Sale::query();

        // Filtros opcionales
        if ($search = $request->input('search')) {
            $sales->where('operationCode', 'like', "%$search%")
                  ->orWhereHas('customer', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  });
        }

        $sales = $sales->paginate($perPage);

        return SaleResource::collection($sales);
    }

    // Almacenar una nueva venta
    public function store(StoreSaleRequest $request)
{
    Gate::authorize('create', Sale::class);

    // Validar los datos de la solicitud
    $validated = $request->validated();

    // Crear la venta solo en la tabla sales
    $sale = Sale::create($validated);

    return response()->json([
        'state' => true,
        'message' => 'Venta registrada correctamente.',
        'sale' => new SaleResource($sale), // Devuelve la venta registrada con el recurso
    ]);
}


    // Mostrar detalles de una venta
    public function show(Sale $sale)
    {
        Gate::authorize('view', $sale);

        return response()->json([
            'state' => true,
            'message' => 'Venta encontrada',
            'sale' => new SaleResource($sale),
        ], 200);
    }

    // Actualizar una venta existente
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        Gate::authorize('update', $sale);

        $validated = $request->validated();

        // Actualizar los datos de la venta
        $sale->update($validated);

        // Si la venta tiene órdenes asociadas, actualizar en la tabla sales_orders
        if (isset($validated['order_ids'])) {
            foreach ($validated['order_ids'] as $orderId) {
                $order = Orders::find($orderId);
                if ($order) {
                    // Si la relación de sales_orders ya existe, actualizarla, si no, crear una nueva
                    SalesOrder::updateOrCreate(
                        ['idSale' => $sale->id, 'idOrder' => $order->id],
                        ['subtotal' => $order->total]
                    );
                }
            }
        }

        return response()->json([
            'state' => true,
            'message' => 'Venta actualizada correctamente.',
            'sale' => new SaleResource($sale),
        ]);
    }

    // Eliminar una venta
    public function destroy(Sale $sale)
    {
        Gate::authorize('delete', $sale);

        // Eliminar las relaciones en sales_orders
        $sale->salesOrders()->delete();

        // Eliminar la venta
        $sale->delete();

        return response()->json([
            'state' => true,
            'message' => 'Venta eliminada correctamente',
        ]);
    }

    
}

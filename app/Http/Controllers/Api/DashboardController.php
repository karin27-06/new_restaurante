<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\OrderDishes;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Sale; 
use App\Models\Table; // Asegúrate de incluir el modelo Table
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function getdatos(Request $request)
    {
        // Obtener las fechas de inicio y fin desde los parámetros de la solicitud
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Inicializar la consulta para obtener el total de clientes sin filtro
        $totalCustomers = Customer::count();

        // Inicializar la consulta para obtener el total de clientes dentro del rango de fechas
        $queryCustomers = Customer::query();

        // Si se ha proporcionado una fecha de inicio, aplicar el filtro
        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $queryCustomers->where('created_at', '>=', $startDate);
        }

        // Si se ha proporcionado una fecha de fin, aplicar el filtro
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $queryCustomers->where('created_at', '<=', $endDate);
        }

        // Obtener el total de clientes registrados con el filtro aplicado
        $countInRangeCustomers = $queryCustomers->count();

        // ---- Total de órdenes con estado 'finalizado' ----
        $totalOrders = Orders::where('state', 'finalizado')->count();
        $queryOrders = Orders::where('state', 'finalizado');

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $queryOrders->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $queryOrders->where('created_at', '<=', $endDate);
        }

        // Obtener el total de órdenes con estado 'finalizado' con el filtro aplicado
        $countInRangeOrders = $queryOrders->count();

        // ---- Obtener las mesas más frecuentes ----
        // Obtener el ID de la mesa asociada a las órdenes 'finalizadas' dentro del rango de fechas
        $frequentTables = Orders::where('state', 'finalizado')
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('created_at', '<=', $endDate);
            })
            ->select('idTable') // Seleccionar solo el ID de la mesa
            ->groupBy('idTable') // Agrupar por ID de mesa
            ->orderByRaw('COUNT("idTable") DESC') // Corregir el nombre de la columna
            ->limit(5) // Limitar a las 5 mesas más frecuentes
            ->get();

        // Obtener los números de mesa correspondientes
        $frequentTableNums = $frequentTables->map(function ($order) {
            $table = Table::find($order->idTable); // Obtener la tabla usando el ID
            return $table ? $table->tablenum : null; // Obtener el número de mesa, si existe
        });

        // ---- Monto total de ingresos ----
        $totalIncome = SalesOrder::sum('subtotal');
        $queryIncome = SalesOrder::query();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $queryIncome->whereHas('order', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $queryIncome->whereHas('order', function ($query) use ($endDate) {
                $query->where('created_at', '<=', $endDate);
            });
        }

        $countInRangeIncome = $queryIncome->sum('subtotal');

        // ---- Estadística de métodos de pago ----
        $paymentMethods = ['Yape', 'Efectivo', 'Transferencia', 'Plin', 'Tarjeta'];

       $paymentStats = Sale::select('paymentType', DB::raw('count(*) as count'))
    ->whereIn('paymentType', $paymentMethods)
    ->when($startDate, function ($query) use ($startDate) {
        // Usamos 'salesOrders' en lugar de 'order'
        $query->whereHas('salesOrders', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        });
    })
    ->when($endDate, function ($query) use ($endDate) {
        // Usamos 'salesOrders' en lugar de 'order'
        $query->whereHas('salesOrders', function ($query) use ($endDate) {
            $query->where('created_at', '<=', $endDate);
        });
    })
    ->groupBy('paymentType')
    ->get();


        $paymentMethodStats = $paymentStats->mapWithKeys(function ($item) {
            return [$item->paymentType => $item->count];
        });

        foreach ($paymentMethods as $method) {
            if (!isset($paymentMethodStats[$method])) {
                $paymentMethodStats[$method] = 0;
            }
        }

          // ---- Total de platillos pedidos con estado 'completado' ----
        $totalDishes = OrderDishes::where('state', 'completado')->sum('quantity');
        $queryDishes = OrderDishes::where('state', 'completado');

        if ($startDate) {
            $queryDishes->whereHas('order', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            });
        }

        if ($endDate) {
            $queryDishes->whereHas('order', function ($query) use ($endDate) {
                $query->where('created_at', '<=', $endDate);
            });
        }

        $countInRangeDishes = $queryDishes->sum('quantity');

        // Retornar todos los totales como una respuesta JSON
        return response()->json([
            'totales' => [
                'total_customers' => $totalCustomers,
                'total_orders' => $totalOrders,
                'total_income' => $totalIncome,
                                'total_dishes' => $totalDishes,

            ],
            'total_in_range' => [
                'total_customers' => $countInRangeCustomers,
                'total_orders' => $countInRangeOrders,
                'total_income' => $countInRangeIncome,
                                'total_dishes' => $countInRangeDishes,

            ],
            'frequent_tables' => $frequentTableNums, // Incluir las mesas más frecuentes
            'payment_method_stats' => $paymentMethodStats
        ]);
    }
}

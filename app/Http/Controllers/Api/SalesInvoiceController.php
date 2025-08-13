<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\Sale;  // Modelo Sale
use Illuminate\Http\Request;
use Carbon\Carbon;
class SalesInvoiceController extends Controller
{
    // Método para generar el comprobante (factura o boleta)
    public function generateInvoice(Request $request, $idSale)
    {
        // Validar que el prefijo sea 'boleta' o 'factura'
        $request->validate([
            'prefix' => 'required|in:Boleta,Factura',  // Verificamos que el prefijo sea válido
        ]);

        $prefix = $request->prefix;  // boleta o factura
        $maxNumber = 9999;  // Límite máximo de números en la serie
        $year = Carbon::now()->year;  // Año actual

        // Buscar la última factura/boleta con el mismo prefijo
        $lastInvoice = SalesInvoice::where('prefix', $prefix)
            ->orderBy('number', 'desc')  // Ordenar para obtener el último número de serie
            ->first();

        // Si no existe, comenzamos desde el número 1
        $newNumber = 1;
        if ($lastInvoice) {
            $newNumber = $lastInvoice->number + 1;

            // Si alcanzamos el número máximo, reiniciamos el número
            if ($newNumber > $maxNumber) {
                $newNumber = 1;
            }
        }

        // Formatear el número correlativo con ceros a la izquierda, por ejemplo: 0001, 0002, etc.
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Generar el número de serie con el prefijo y el número correlativo
        $serie = strtoupper(substr($prefix, 0, 1)) . '001-' . $formattedNumber;

        // Registrar el comprobante (factura o boleta)
        $invoice = new SalesInvoice();
        $invoice->idSale = $idSale;  // Asocia la venta con el comprobante
        $invoice->prefix = $prefix;  // 'boleta' o 'factura'
        $invoice->serie = $serie;  // El número de serie generado
        $invoice->number = $newNumber;  // El número correlativo
        $invoice->save();  // Guardamos el comprobante en la base de datos

        // Retornar una respuesta con el comprobante generado
        return response()->json([
            'message' => 'Comprobante generado correctamente.',
            'invoice' => $invoice,  // Enviar el comprobante generado en la respuesta
        ]);
    }
}

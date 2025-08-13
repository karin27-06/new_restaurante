<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\Sale;
use App\Models\SalesInvoice;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use App\Helpers\NumeroALetras;

class SunatController extends Controller
{
    public function enviarFactura(Request $request)
    {
        $see = require base_path('config/greenter.php');

        // Obtener los datos de la venta
        $idSale = $request->idSale;
        $sale = SalesOrder::with(['salesInvoice', 'order.orderDishes', 'order.customer'])->find($idSale);
        $sales = Sale::with(['salesOrders', 'salesOrders.salesInvoice', 'salesOrders.order.orderDishes', 'salesOrders.order.customer'])->find($idSale);  // Usamos el modelo 'Sale'

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $total = $sale->subtotal;
        $igv = $total * 0.10;
        $subtotal = $total - $igv;
        $saleInvoice = $sale->salesInvoice;
        // Obtener los datos del cliente desde la venta
        $customer = $sale->sale->customer;
        $serieCompleta = $saleInvoice->serie; // F001-0001
        $serie = explode('-', $serieCompleta)[0]; // Obtienes 'F001'
        $correlativo = str_pad($saleInvoice->number, 4, '0', STR_PAD_LEFT); // Asegura que el número correlativo tenga 4 dígitos con ceros a la izquierda

        // Verificar el formato de la serie y correlativo
        if (!preg_match('/^[A-Za-z]{1}\d{3}$/', $serie)) {
            // La serie debe estar en el formato 'F001' o 'B001' (1 letra seguida de 3 números)
            return response()->json(['error' => 'El formato de la serie es incorrecto.'], 400);
        }

        if (!preg_match('/^\d{4}$/', $correlativo)) {
            // El correlativo debe tener 4 dígitos
            return response()->json(['error' => 'El formato del correlativo es incorrecto.'], 400);
        }

        // Emisor
        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('LIMA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. Villa Nueva 221')
            ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.


        $company = (new Company())
            ->setRuc('20123456789')
            ->setRazonSocial('GREEN SAC')
            ->setNombreComercial('GREEN')
            ->setAddress($address);


        if ($customer->client_type_id == 1) {
            $tipoDocCliente = 1;
            $client = (new Client())
                ->setTipoDoc($tipoDocCliente)
                ->setNumDoc($customer->codigo)
                ->setRznSocial($customer->name);


            // Venta
            $invoice = (new Invoice())
                ->setUblVersion('2.1')
                ->setTipoOperacion('0101') // Venta - Catalog. 51
                ->setTipoDoc('03') // Factura - Catalog. 01 
                ->setSerie($serie)
                ->setCorrelativo($correlativo)
                ->setFechaEmision(now()) // Zona horaria: Lima
                ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
                ->setTipoMoneda('PEN') // Sol - Catalog. 02
                ->setCompany($company)
                ->setClient($client)
                ->setMtoOperGravadas($subtotal)
                ->setMtoIGV($igv)
                ->setTotalImpuestos($igv)
                ->setValorVenta($subtotal)
                ->setSubTotal($total)
                ->setMtoImpVenta($total);
        } else {
            $tipoDocCliente = 6;
            $client = (new Client())
                ->setTipoDoc($tipoDocCliente)
                ->setNumDoc($customer->codigo)
                ->setRznSocial($customer->name);


            // Venta
            $invoice = (new Invoice())
                ->setUblVersion('2.1')
                ->setTipoOperacion('0101') // Venta - Catalog. 51
                ->setTipoDoc('01') // Factura - Catalog. 01 
                ->setSerie($serie)
                ->setCorrelativo($correlativo)
                ->setFechaEmision(now()) // Zona horaria: Lima
                ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
                ->setTipoMoneda('PEN') // Sol - Catalog. 02
                ->setCompany($company)
                ->setClient($client)
                ->setMtoOperGravadas($subtotal)
                ->setMtoIGV($igv)
                ->setTotalImpuestos($igv)
                ->setValorVenta($subtotal)
                ->setSubTotal($total)
                ->setMtoImpVenta($total);
        }


        foreach ($sale->order->orderDishes as $plato) {
            // Validar que el nombre del plato no esté vacío
            $descripcionPlato = !empty($plato->name) ? $plato->name : 'Producto sin descripción';
            $totalplato = $sale->subtotal;
            $igv = $total * 0.10;
            $subtotalplato = $total - $igv;
            $item = (new SaleDetail())
                ->setCodProducto($plato->id)
                ->setUnidad('NIU') // Unidad - Catalog. 03
                ->setCantidad($plato->quantity) // Asumo cantidad 1 para el cálculo
                ->setMtoValorUnitario($subtotalplato) // Subtotal por unidad
                ->setDescripcion($descripcionPlato) // Descripción del plato
                ->setMtoBaseIgv($subtotalplato) // Monto base de IGV
                ->setPorcentajeIgv(10.00) // 10% IGV
                ->setIgv($igv) // Valor del IGV
                ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos($igv) // Suma de impuestos en el detalle
                ->setMtoValorVenta($subtotalplato) // Subtotal
                ->setMtoPrecioUnitario($totalplato); // Total
        }



        // Usar la librería NumeroALetras para convertir el total a letras
        $numeroALetras = new NumeroALetras();
        $letras = $numeroALetras->convertir($total, true); // true indica que se incluye "Soles"

        // Crear la leyenda
        $legend = (new Legend())
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue('SON ' . $letras); // "SON" seguido del total en letras

        $invoice->setDetails([$item])
            ->setLegends([$legend]);


        $result = $see->send($invoice);

$folderPath = storage_path('app/sunat/comprobantes');

// Crear carpeta si no existe
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Guardar XML
file_put_contents(
    $folderPath . '/' . $invoice->getName() . '.xml',
    $see->getFactory()->getLastXml()
);


        // Verificamos que la conexión con SUNAT fue exitosa.
        if (!$result->isSuccess()) {
            // Mostrar error al conectarse a SUNAT.
            echo 'Codigo Error: ' . $result->getError()->getCode();
            echo 'Mensaje Error: ' . $result->getError()->getMessage();
            exit();
        }


  // Guardar ZIP del CDR
file_put_contents(
    $folderPath . '/R-' . $invoice->getName() . '.zip',
    $result->getCdrZip()
);

        $cdr = $result->getCdrResponse();

        $code = (int)$cdr->getCode();

       // Actualizamos el estado en la tabla 'sales' dependiendo del resultado
        if ($code === 0) {
            echo 'ESTADO: ACEPTADA' . PHP_EOL;
            // Actualizamos el estado en la base de datos
            $sales->stateSunat = 'aprobado';
            $sales->save();
        } else if ($code >= 2000 && $code <= 3999) {
            echo 'ESTADO: RECHAZADA' . PHP_EOL;
            // Actualizamos el estado en la base de datos
            $sales->stateSunat = 'rechazado';
            $sales->save();
        } else {
            echo 'Excepción' . PHP_EOL;
        }

        echo $cdr->getDescription() . PHP_EOL;
    }
}

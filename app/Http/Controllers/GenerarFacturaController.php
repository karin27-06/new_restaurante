<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

class GenerarFacturaController extends Controller
{
    public function generarFactura()
    {
        // Cargar configuración
        $see = require base_path('config/greenter.php');  // Cargar la configuración de Greenter

        // Cliente
        $client = (new Client())
            ->setTipoDoc('6')  // Tipo de documento (6 = DNI)
            ->setNumDoc('20000000001')  // Número de documento del cliente
            ->setRznSocial('EMPRESA X');  // Razón social del cliente

        // Emisor
        $address = (new Address())
            ->setUbigueo('150101')  // Código de ubicación (Ubigeo)
            ->setDepartamento('LIMA')  // Departamento
            ->setProvincia('LIMA')  // Provincia
            ->setDistrito('LIMA')  // Distrito
            ->setUrbanizacion('-')  // Urbanización (si aplica)
            ->setDireccion('Av. Villa Nueva 221')  // Dirección de la empresa
            ->setCodLocal('0000');  // Código de establecimiento asignado por SUNAT (0000 por defecto)

        $company = (new Company())
            ->setRuc('20123456789')  // RUC de la empresa
            ->setRazonSocial('GREEN SAC')  // Razón social de la empresa
            ->setNombreComercial('GREEN')  // Nombre comercial
            ->setAddress($address);  // Dirección de la empresa

        // Venta
        $invoice = (new Invoice())
            ->setUblVersion('2.1')  // Versión UBL
            ->setTipoOperacion('0101')  // Tipo de operación: Venta
            ->setTipoDoc('01')  // Tipo de documento: Factura
            ->setSerie('F001')  // Serie de la factura
            ->setCorrelativo('1')  // Correlativo de la factura
            ->setFechaEmision(new \DateTime('2020-08-24 13:05:00-05:00'))  // Fecha de emisión
            ->setFormaPago(new FormaPagoContado())  // Forma de pago: Contado
            ->setTipoMoneda('PEN')  // Moneda: Sol (PEN)
            ->setCompany($company)  // Datos de la empresa
            ->setClient($client)  // Datos del cliente
            ->setMtoOperGravadas(10.00)  // Monto de operaciones gravadas
            ->setMtoIGV(1.80)  // Monto de IGV (18% de 10.00)
            ->setTotalImpuestos(1.80)  // Total de impuestos
            ->setValorVenta(10.00)  // Valor de la venta (10 soles)
            ->setSubTotal(11.80)  // Subtotal (valor venta + IGV)
            ->setMtoImpVenta(11.80);  // Monto total de la venta

        // Detalle de la venta
        $item = (new SaleDetail())
            ->setCodProducto('P001')  // Código del producto
            ->setUnidad('NIU')  // Unidad de medida
            ->setCantidad(1)  // Cantidad
            ->setMtoValorUnitario(10.00)  // Monto unitario
            ->setDescripcion('Producto de prueba')  // Descripción del producto
            ->setMtoBaseIgv(10.00)  // Base para el cálculo del IGV
            ->setPorcentajeIgv(18.00)  // Porcentaje de IGV
            ->setIgv(1.80)  // Monto del IGV
            ->setTipAfeIgv('10')  // Tipo de afectación del IGV: Gravado
            ->setTotalImpuestos(1.80)  // Total de impuestos
            ->setMtoValorVenta(10.00)  // Monto de venta
            ->setMtoPrecioUnitario(11.80);  // Precio unitario con IGV incluido

        // Leyenda (monto en letras)
        $legend = (new Legend())
            ->setCode('1000')  // Código de la leyenda
            ->setValue('SON ONCE CON 80/100 SOLES');  // Monto en letras

        // Asignamos los detalles y las leyendas
        $invoice->setDetails([$item])
                ->setLegends([$legend]);

        // Enviar la factura a SUNAT
        $result = $see->send($invoice);

        // Guardar archivos XML y CDR en la carpeta de comprobantes
        file_put_contents(storage_path('app/comprobantes/'.$invoice->getName().'.xml'), $see->getFactory()->getLastXml());
        file_put_contents(storage_path('app/comprobantes/R-'.$invoice->getName().'.zip'), $result->getCdrZip());

        // Devolver respuesta
        return response()->json([
            'message' => 'Factura generada exitosamente.',
            'xml' => storage_path('app/comprobantes/'.$invoice->getName().'.xml'),
            'cdr' => storage_path('app/comprobantes/R-'.$invoice->getName().'.zip'),
        ]);
    }
}

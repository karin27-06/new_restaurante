<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use TCPDF;

class CustomerPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los clientes y convertirlos en un array para facilitar el manejo
        $customers = Customer::orderBy('id', 'asc')->get();

        $customersArray = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'codigo' => $customer->codigo,
                'clienteTipo_name' => $customer->clienteType->name,
                'state' => $customer->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Clientes');
        $pdf->SetSubject('Reporte de Clientes');

        // Configuración de márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10); // Asegura que haya espacio entre las filas y el pie de la página

        // Eliminar la línea de encabezado (borde superior)
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);

        // Personalizar el pie de página (eliminar línea predeterminada)
        $pdf->setFooterData(array(0, 0, 0), array(255, 255, 255));

        // Agregar la primera página
        $pdf->AddPage();

        // Encabezado del PDF
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Clientes', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Código', 'Tipo de cliente', 'Estado', 'Creación', 'Actualización'];
        $widths = [10, 40, 30, 30, 18, 30, 30]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 9, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los clientes
        $pdf->SetFont('helvetica', '', 7);

        foreach ($customersArray as $customer) {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 7, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

            // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 7);
            $pdf->MultiCell($widths[0], 7, $customer['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 7, $customer['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 7, $customer['codigo'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 7, $customer['clienteTipo_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 7, $customer['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 7, $customer['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 7, $customer['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Clientes.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Clientes.pdf"');
    }
}

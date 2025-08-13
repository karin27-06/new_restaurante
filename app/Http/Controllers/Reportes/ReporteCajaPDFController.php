<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\ReporteCaja;
use TCPDF;

class ReporteCajaPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los reportes de cajas y convertirlos en un array para facilitar el manejo
        $reporte_cajas = ReporteCaja::orderBy('id', 'asc')->get();

        $reporte_cajas_Array = $reporte_cajas->map(function ($reporte_caja) {
            return [
                'id' => $reporte_caja->id,
                'numero_cajas' => $reporte_caja->caja->numero_cajas,
                'vendedorNombre' => $reporte_caja->vendedor->name . ' ' . $reporte_caja->vendedor->apellidos,
                'monto_efectivo' => $reporte_caja->monto_efectivo,
                'monto_tarjeta' => $reporte_caja->monto_tarjeta,
                'monto_yape' => $reporte_caja->monto_yape,
                'monto_transferencia' => $reporte_caja->monto_transferencia,
                'created_at' => $reporte_caja->created_at,
                'updated_at' => $reporte_caja->updated_at,
                'fecha_salida'=> $reporte_caja->fecha_salida,
            ];

        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Reportes de cajas');
        $pdf->SetSubject('Reporte de Cajas');

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
        $pdf->Cell(0, 20, 'Lista de Reporte de Cajas', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'N° de Caja', 'Vendedor', 'Monto en efectivo', 'Monto en tarjeta', 'Monto en Yape o Plin', 'Monto en transferencia', 'Fecha de apertura', 'Fecha de modificacion', 'Fecha de cierre'];
        $widths = [5, 12, 30, 18, 18, 18, 18, 25, 25, 25]; // Tamaños ajustados para las celdas

        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 7, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los reportes de cajas
        $pdf->SetFont('helvetica', '', 5);

        foreach ($reporte_cajas_Array as $reporte_caja) {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 5);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 5, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

            // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 5);
            $pdf->MultiCell($widths[0], 5, $reporte_caja['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 5, $reporte_caja['numero_cajas'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 5, $reporte_caja['vendedorNombre'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 5, 'S/.' . number_format($reporte_caja['monto_efectivo'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 5, 'S/.' . number_format($reporte_caja['monto_tarjeta'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 5, 'S/.' . number_format($reporte_caja['monto_yape'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 5, 'S/.' . number_format($reporte_caja['monto_transferencia'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 5, $reporte_caja['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[8], 5, $reporte_caja['updated_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[9], 5, $reporte_caja['fecha_salida'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('reporteCajas.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="reporteCajas.pdf"');
    }
}

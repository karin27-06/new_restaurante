<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use TCPDF;

class PresentationPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de las presentaciones y convertirlos en un array para facilitar el manejo
        $presentations = Presentation::orderBy('id', 'asc')->get();

        $presentationsArray = $presentations->map(function ($presentation) {
            return [
                'id' => $presentation->id,
                'name' => $presentation->name,
                'description' => $presentation->description,
                'state' => $presentation->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $presentation->created_at,
                'updated_at' => $presentation->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Presentaciones');
        $pdf->SetSubject('Reporte de Presentaciones');

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
        $pdf->Cell(0, 20, 'Lista de Presentaciones', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Descripción', 'Estado', 'Creación', 'Actualización'];
        $widths = [7, 30, 66, 17, 35, 35]; // Tamaños adecuados para las celdas

        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 8, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de las presentaciones
        $pdf->SetFont('helvetica', '', 8);

        foreach ($presentationsArray as $presentation) {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 8, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

            // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell($widths[0], 8, $presentation['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 8, $presentation['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 8, $presentation['description'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 8, $presentation['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 8, $presentation['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 8, $presentation['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Presentaciones.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Presentaciones.pdf"');
    }
}

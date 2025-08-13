<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Table;
use TCPDF;

class TablePDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de las mesas y convertirlos en un array para facilitar el manejo
        $tables = Table::orderBy('id', 'asc')->get();

        $tablesArray = $tables->map(function ($table) {
            return [
                'id' => $table->id,
                'name' => $table->name,
                'tablenum' => $table->tablenum,
                'capacity' => $table->capacity,
                'area_name' => $table->area?->name,
                'floor_name' => $table->floor?->name,
                'state' => $table->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Mesas');
        $pdf->SetSubject('Reporte de Mesas');

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
        $pdf->Cell(0, 20, 'Lista de Mesas', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Número', 'Capacidad', 'Area', 'Piso', 'Estado', 'Creación', 'Actualización'];
        $widths = [6, 25, 20, 20, 25, 30, 15, 25, 25]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 7, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de las mesas
        $pdf->SetFont('helvetica', '', 6);

        foreach ($tablesArray as $table) {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 6);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 6, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

            // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 6);
            $pdf->MultiCell($widths[0], 6, $table['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 6, $table['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 6, $table['tablenum'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 6, $table['capacity'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 6, $table['area_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 6, $table['floor_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 6, $table['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 6, $table['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[8], 6, $table['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Mesas.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Mesas.pdf"');
    }
}

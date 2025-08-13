<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use TCPDF;

class AlmacenPDFController extends Controller
{
    public function exportPDF()
    {
        $almacenes = Almacen::orderBy('id', 'asc')->get();

        $almacenesArray = $almacenes->map(function($almacen){
            return [
                'id' => $almacen->id,
                'name' => $almacen->name,
                'state' => $almacen->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $almacen->created_at,
                'updated_at' => $almacen->updated_at,
            ];
            })->toArray();

            // Crear el objeto TCPDF
        $pdf = new TCPDF(); 

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Almacenes');
        $pdf->SetSubject('Reporte de Almacenes');

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
        $pdf->Cell(0, 20, 'Lista de Almacenes', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados
        
        $header = ['ID', 'Nombre', 'Estado', 'Creación', 'Actualización'];
        $widths = [10, 70, 30, 40, 40]; // Tamaños adecuados para las celdas
    
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los almacenes
        $pdf->SetFont('helvetica', '', 10);

        foreach ($almacenesArray as $almacen)  {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

             // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($widths[0], 10, $almacen['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $almacen['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $almacen['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $almacen['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $almacen['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
    
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Almacenes.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Almacenes.pdf"');
    }
}
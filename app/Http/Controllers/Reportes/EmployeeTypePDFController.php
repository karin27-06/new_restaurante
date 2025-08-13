<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use TCPDF;

class EmployeeTypePDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los tipos de empleado y convertirlos en un array para facilitar el manejo
        $employeeTypes = EmployeeType::orderBy('id', 'asc')->get();

        $employeeTypesArray = $employeeTypes->map(function ($employeeType) {
            return [
                'id' => $employeeType->id,
                'name' => $employeeType->name,
                'state' => $employeeType->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $employeeType->created_at,
                'updated_at' => $employeeType->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Tipos de Empleados');
        $pdf->SetSubject('Reporte de Tipos de Empleados');

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
        $pdf->Cell(0, 20, 'Lista de Tipos de Empleados', 0, 1, 'C');

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

        // Imprimir los datos de los tipos de empleados
        $pdf->SetFont('helvetica', '', 10);

        foreach ($employeeTypesArray as $employeeType) {
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
            $pdf->MultiCell($widths[0], 10, $employeeType['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $employeeType['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $employeeType['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $employeeType['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $employeeType['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Tipos_empleado.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Tipos_empleado.pdf"');
    }
}

<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use TCPDF;

class EmployeePDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los empleados y convertirlos en un array para facilitar el manejo
        $employees = Employee::orderBy('id', 'asc')->get();

        $employeesArray = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'codigo' => $employee->codigo,
                'empleadoTipo_name' => $employee->empleadoType->name,
                'state' => $employee->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $employee->created_at,
                'updated_at' => $employee->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Empleados');
        $pdf->SetSubject('Reporte de Empleados');

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
        $pdf->Cell(0, 20, 'Lista de Empleados', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Código', 'Tipo de empleado', 'Estado', 'Creación', 'Actualización'];
        $widths = [10, 40, 30, 30, 18, 30, 30]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 9, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los empleados
        $pdf->SetFont('helvetica', '', 7);

        foreach ($employeesArray as $employee) {
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
            $pdf->MultiCell($widths[0], 7, $employee['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 7, $employee['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 7, $employee['codigo'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 7, $employee['empleadoTipo_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 7, $employee['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 7, $employee['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 7, $employee['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Empleados.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Empleados.pdf"');
    }
}

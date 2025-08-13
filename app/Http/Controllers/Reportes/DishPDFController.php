<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Dishes;
use TCPDF;

class DishPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los platos y convertirlos en un array para facilitar el manejo
        $dishes = Dishes::orderBy('id', 'asc')->get();

        $dishesArray = $dishes->map(function ($dish) {
            return [
                'id' => $dish->id,
                'name' => $dish->name,
                'price' => $dish->price,
                'quantity' => $dish->quantity,
                'category_name' => $dish->category->name ?? 'Sin Categoria',
                'state' => $dish->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $dish->created_at,
                'updated_at' => $dish->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Platos');
        $pdf->SetSubject('Reporte de Platos');

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
        $pdf->Cell(0, 20, 'Lista de Platos', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Precio', 'Cantidad', 'Categoria', 'Estado', 'Creación', 'Actualización'];
        $widths = [7, 40, 23, 20, 27, 15, 30, 30]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 9, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los platos
        $pdf->SetFont('helvetica', '', 7);

        foreach ($dishesArray as $dish) {
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
            $pdf->MultiCell($widths[0], 7, $dish['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 7, $dish['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 7, 'S/. ' . number_format($dish['price'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 7, $dish['quantity'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 7, $dish['category_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 7, $dish['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 7, $dish['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 7, $dish['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Platos.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Platos.pdf"');
    }
}

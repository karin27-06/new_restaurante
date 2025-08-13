<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Product;
use TCPDF;

class ProductPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los productos y convertirlos en un array para facilitar el manejo
        $products = Product::orderBy('id', 'asc')->get();

        $productsArray = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'details' => $product->details,
                'category_name' => $product->category->name,
                'almacen_name' => $product->almacen->name,
                'state' => $product->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Productos');
        $pdf->SetSubject('Reporte de Productos');

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
        $pdf->Cell(0, 20, 'Lista de Productos', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Detalles', 'Categoria', 'Almacen', 'Estado', 'Creación', 'Actualización'];
        $widths = [7, 40, 30, 20, 20, 15, 30, 30]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 9, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los productos
        $pdf->SetFont('helvetica', '', 7);

        foreach ($productsArray as $product) {
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
            $pdf->MultiCell($widths[0], 7, $product['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 7, $product['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 7, $product['details'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 7, $product['category_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 7, $product['almacen_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 7, $product['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 7, $product['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 7, $product['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Productos.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Productos.pdf"');
    }
}

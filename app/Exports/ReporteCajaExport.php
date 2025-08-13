<?php

namespace App\Exports;

use App\Models\ReporteCaja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteCajaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return ReporteCaja::orderBy('id', 'asc')->get();  // Traemos todas los datos de los reportes ordenados por ID
    }

  public function map($reporte_caja): array
{
    return [
        $reporte_caja->id,
        $reporte_caja->caja->numero_cajas,
        $reporte_caja->vendedor->name . ' ' . $reporte_caja->vendedor->apellidos,
        $reporte_caja->monto_efectivo,
        $reporte_caja->monto_tarjeta,
        $reporte_caja->monto_yape,
        $reporte_caja->monto_transferencia,
        $reporte_caja->created_at->format('d-m-Y H:i:s'), // Fecha de apertura
        $reporte_caja->updated_at->format('d-m-Y H:i:s'),  // Fecha de modificacion formateada
    ];
}
    public function headings(): array
    {
        // Este array define los encabezados en la fila 3
    return [
        ['LISTA DE REPORTES DE CAJAS', '', '', '', '', '', '', '', ''],  // Fila 1 con el título
        [],  // Fila 2 en blanco (espaciado entre el título y los encabezados)
        ['ID', 'N° de Caja', 'Vendedor', 'Monto en efectivo', 'Monto en tarjeta', 'Monto en Yape o Plin', 'Monto en transferencia', 'Fecha de apertura', 'Fecha de modificacion']  // Fila 3 con los encabezados
    ];

    }
    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para las celdas
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true,'size' => 14],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3'], // Color de fondo azul claro para el título
            ],
        ]);

        // Estilo para los encabezados de la tabla
        $sheet->getStyle('A3:I3')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D9EAD3'], // Color de fondo para los encabezados
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        ]);

        // Estilo para las filas de datos
        $sheet->getStyle('A4:I' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Estilo específico para la columna de 'Monto efectivo' (columna D)
        $sheet->getStyle('D4:D' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');
         // Estilo específico para la columna de 'Monto tarjeta' (columna E)
        $sheet->getStyle('E4:E' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');
        // Estilo específico para la columna de 'Monto yape/plin' (columna F)
        $sheet->getStyle('F4:F' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');
         // Estilo específico para la columna de 'Monto transferencia' (columna G)
        $sheet->getStyle('G4:G' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        // Ajuste de las columnas para darles más espacio
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        return [];
    }
}

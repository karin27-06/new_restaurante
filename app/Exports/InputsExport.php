<?php

namespace App\Exports;

use App\Models\Input;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InputsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Input::orderBy('id', 'asc')->get();  // Traemos todas los insumos ordenados por ID
    }

    public function map($input): array
    {
        return [
            $input->id,
            $input->name,
            $input->description,
            $input->priceSale,
            $input->quantityUnitMeasure,
            $input->unitMeasure,
            $input->almacen->name,
            $input->state == 1 ? 'Activo' : 'Inactivo',
            $input->created_at->format('d-m-Y H:i:s'), // Fecha de creación formateada
            $input->updated_at->format('d-m-Y H:i:s')  // Fecha de actualización formateada
        ];
    }
    public function headings(): array
    {
        // Este array define los encabezados en la fila 3
    return [
        ['LISTA DE INSUMOS', '', '', '', '', '', '', '', '', ''],  // Fila 1 con el título
        [],  // Fila 2 en blanco (espaciado entre el título y los encabezados)
        ['ID', 'Nombre', 'Descripcion', 'Precio de Venta', 'Cantidad por medida', 'Unidad de Medida', 'Almacen', 'Estado', 'Creación', 'Actualización']  // Fila 3 con los encabezados
    ];

    }
    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para las celdas
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true,'size' => 14],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3'], // Color de fondo azul claro para el título
            ],
        ]);

        // Estilo para los encabezados de la tabla
        $sheet->getStyle('A3:J3')->applyFromArray([
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
        $sheet->getStyle('A4:J' . $sheet->getHighestRow())->applyFromArray([
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
        // Estilo específico para la columna de 'Costo' (columna C)
        $sheet->getStyle('D4:D' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');
         // Estilo específico para la columna de 'Costo' (columna C)
        $sheet->getStyle('E4:E' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');
        
        // Ajuste de las columnas para darles más espacio
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        return [];
    }
}

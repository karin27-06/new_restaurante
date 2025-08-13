<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Employee::orderBy('id', 'asc')->get();  // Traemos todas los empleados ordenadas por ID
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->name,
            $employee->codigo,
            $employee->empleadoType->name,
            $employee->state == 1 ? 'Activo' : 'Inactivo',
            $employee->created_at->format('d-m-Y H:i:s'), // Fecha de creación formateada
            $employee->updated_at->format('d-m-Y H:i:s')  // Fecha de actualización formateada
        ];
    }   
    public function headings(): array
    {
        // Este array define los encabezados en la fila 3
    return [
        ['LISTA DE EMPLEADOS', '', '', '', '', '', ''],  // Fila 1 con el título
        [],  // Fila 2 en blanco (espaciado entre el título y los encabezados)
        ['ID', 'Nombre', 'Código', 'Tipo de empleado', 'Estado', 'Creación', 'Actualización']  // Fila 3 con los encabezados
    ];

    }
    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para las celdas
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true,'size' => 14],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CFE2F3'], // Color de fondo azul claro para el título
            ],
        ]);

        // Estilo para los encabezados de la tabla
        $sheet->getStyle('A3:G3')->applyFromArray([
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
        $sheet->getStyle('A4:G' . $sheet->getHighestRow())->applyFromArray([
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
        // Ajuste de las columnas para darles más espacio
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        return [];
    }
}

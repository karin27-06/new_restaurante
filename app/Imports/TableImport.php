<?php

namespace App\Imports;

use App\Models\Table;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class TableImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Table::create([
                'name' => $row['nombre'],
                'tablenum' => $row['numero'],
                'capacity' => $row['capacidad'],
                'idArea' => $row['area'],
                'idFloor' => $row['piso'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}

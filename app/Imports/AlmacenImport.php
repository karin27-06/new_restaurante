<?php

namespace App\Imports;

use App\Models\Almacen;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AlmacenImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Almacen::create([
                'name' => $row['nombre'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}
<?php

namespace App\Imports;

use App\Models\EmployeeType;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmployeeTypeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            EmployeeType::create([
                'name' => $row['nombre'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}

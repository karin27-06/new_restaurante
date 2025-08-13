<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SupplierImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Supplier::create([
                'name' => $row['nombre'],
                'address' => $row['dirección'],
                'ruc' => $row['ruc'],
                'phone' => $row['teléfono'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}

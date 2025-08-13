<?php

namespace App\Imports;

use App\Models\Dishes;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class DishImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Dishes::create([
                'name' => $row['nombre'],
                'price' => $row['precio'],
                'quantity' => $row['cantidad'],
                'idCategory' => $row['categoria'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}

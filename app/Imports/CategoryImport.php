<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CategoryImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Category::create([
                'name' => $row['nombre'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}

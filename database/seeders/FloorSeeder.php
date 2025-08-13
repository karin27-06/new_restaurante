<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $pisos = [
            ['name' => 'Piso 1', 'description' => 'Primer nivel del local', 'state' => true],
            ['name' => 'Piso 2', 'description' => 'Segundo nivel del local', 'state' => true],
            ['name' => 'Piso 3', 'description' => 'Tercer nivel del local', 'state' => true],
        ];

        foreach ($pisos as $piso) {
            Floor::create($piso);
        }
    }
}

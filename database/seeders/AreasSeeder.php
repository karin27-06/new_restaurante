<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Areas;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Zona Interior', 'state' => true],
            ['name' => 'Terraza', 'state' => true],
            ['name' => 'Salón VIP', 'state' => true],
            ['name' => 'Patio', 'state' => true],
            ['name' => 'Balcón', 'state' => true],
        ];

        foreach ($areas as $area) {
            Areas::create($area);
        }
    }
}

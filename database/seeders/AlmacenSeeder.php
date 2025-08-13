<?php

namespace Database\Seeders;

use App\Models\Almacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder{
    public function run(): void{
        Almacen::create([
            'name' => 'Almacen Principal',
            'state' => true,
        ]);
        Almacen::create([
            'name' => 'Almacen Secundario',
            'state' => true,
        ]);
        // Crear almacenes aleatorios usando la factory con nombres únicos (por ejemplo, 500 registros)
        //Almacen::factory(500)->create(); // Genera 500 almacenes con nombres únicos
    }
}

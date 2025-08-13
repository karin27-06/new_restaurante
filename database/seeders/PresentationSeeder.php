<?php

namespace Database\Seeders;

use App\Models\Presentation;
use Illuminate\Database\Seeder;

class PresentationSeeder extends Seeder{
    public function run(): void{
        Presentation::create([
            'name' => 'Bolsas',
            'description' => 'Presentación en bolsas plásticas o de papel para empaques pequeños.',
            'state' => true,
        ]);
        Presentation::create([
            'name' => 'Cajas',
            'description' => 'Presentación en cajas de cartón para embalaje y transporte seguro.',
            'state' => true,
        ]);
        Presentation::create([
            'name' => 'Paquetes',
            'description' => 'Presentación en paquetes combinados para facilitar la distribución.',
            'state' => true,
        ]);
        Presentation::create([
            'name' => 'Sacos',
            'description' => 'Presentación en sacos resistentes para productos a granel o pesados.',
            'state' => true,
        ]);
        //Presentation::factory(10000)->create();
    }
}

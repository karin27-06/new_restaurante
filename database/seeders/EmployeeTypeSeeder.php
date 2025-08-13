<?php

namespace Database\Seeders;

use App\Models\EmployeeType;
use Illuminate\Database\Seeder;

class EmployeeTypeSeeder extends Seeder{
    public function run(): void{
        EmployeeType::create([
            'name' => 'Jefe de Cocina',
            'state' => true,
        ]);
        EmployeeType::create([
            'name' => 'Cocinero',
            'state' => true,
        ]);
        EmployeeType::create([
            'name' => 'Ayudante de Cocina',
            'state' => true,
        ]);
        EmployeeType::create([
            'name' => 'Repartidor delivery',
            'state' => true,
        ]);
        //EmployeeType::factory(600)->create();
    }
}

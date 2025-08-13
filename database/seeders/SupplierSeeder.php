<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder{
    public function run(): void{
        Supplier::create([
            'name' => 'Distribuciones Alimenticias Piura S.A.',
            'address' => 'Av. San Martín 123, Piura',
            'ruc' => '20123456789',
            'phone' => '073123456',
            'state' => true,
        ]);
        Supplier::create([
            'name' => 'Proveedora Gastronómica del Norte',
            'address' => 'Calle Lima 456, Piura',
            'ruc' => '20198765432',
            'phone' => '986753951',
            'state' => true,
        ]);
        Supplier::create([
            'name' => 'Insumos para Restaurantes SAC',
            'address' => 'Jr. Piura 789, Piura',
            'ruc' => '20111222333',
            'phone' => '073112233',
            'state' => true,
        ]);
        Supplier::create([
            'name' => 'Alimentos y Bebidas del Pacífico',
            'address' => 'Av. Grau 321, Piura',
            'ruc' => '20144556677',
            'phone' => '974236152',
            'state' => true,
        ]);
        Supplier::create([
            'name' => 'Distribuciones Gourmet SAC',
            'address' => 'Calle Bolívar 654, Piura',
            'ruc' => '20177889900',
            'phone' => '073778899',
            'state' => true,
        ]);
        // Crear Proveedores aleatorios usando la factory con nombres únicos (por ejemplo, 500 registros)
        //Supplier::factory(10000)->create(); // Genera 500 proveedores con nombres únicos
    }
}

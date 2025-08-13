<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Input;
use App\Models\Almacen;
use App\Models\Supplier;

class InputSeeder extends Seeder
{
    public function run(): void
    {
        if (Almacen::count() === 0 || Supplier::count() === 0) {
            $this->command->warn('⚠️ No hay almacenes o proveedores en la base de datos. Ejecuta los seeders correspondientes primero.');
            return;
        }

        Input::factory()->count(8)->create(); 
    }
}

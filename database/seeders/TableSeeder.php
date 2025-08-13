<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Table;
use App\Models\Areas;
use App\Models\Floor;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate que existan áreas y pisos antes de ejecutar esto
        if (Areas::count() === 0 || Floor::count() === 0) {
            $this->command->warn('⚠️ No hay áreas o pisos en la base de datos. Ejecuta los seeders correspondientes primero.');
            return;
        }

        Table::factory()->count(10)->create(); 
    }
}

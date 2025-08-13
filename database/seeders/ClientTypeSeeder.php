<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder{
    public function run(): void{
       ClientType::create([
            'name' => 'Persona natural', //DNI
            'state' => true,
        ]);
        ClientType::create([
            'name' => 'Persona jurídica', //RUC(10 O 20)
            'state' => true,
        ]);
        //ClientType::factory(600)->create();
    }
}

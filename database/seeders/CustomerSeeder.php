<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Anónimo',
                'codigo' => '00000000',
                'client_type_id' => 1,
                'state' => true,
            ]
        );
    }
}

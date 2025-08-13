<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'codigo' => $this->faker->unique()->numerify('########'), // 11 dígitos
            'client_type_id' => 1, // Ajustar según lo disponible en client_types
            'state' => true,
        ];
    }
}

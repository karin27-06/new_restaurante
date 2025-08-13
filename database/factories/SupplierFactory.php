<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'name' es único
            'name' => $this->faker->unique()->company(),
            // Dirección generada, opcional
            'address' => $this->faker->address(),
            // 'ruc' único de 11 dígitos
            'ruc' => $this->faker->unique()->numerify('###########'), // 11 dígitos
            // 'phone' de 9 o 11 dígitos sin caracteres especiales
            'phone' => $this->faker->numerify($this->faker->randomElement(['#########', '###########'])), // 9 o 11 dígitos
            // Estado booleano aleatorio
            'state' => $this->faker->boolean(),
        ];
    }
}

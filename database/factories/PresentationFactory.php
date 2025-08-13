<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PresentationFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'name' es único
            'name' => $this->faker->unique()->company(),
            // 'description' puede ser vacía o generada aleatoriamente
            'description' => $this->faker->optional()->sentence(), // La descripción puede ser nula o una frase aleatoria
            // 'state' es booleano
            'state' => $this->faker->boolean(),
        ];
    }
}

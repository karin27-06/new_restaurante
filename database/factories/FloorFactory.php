<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FloorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'state'       => $this->faker->boolean(90),
        ];
    }
}


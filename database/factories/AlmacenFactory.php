<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class AlmacenFactory extends Factory{
    public function definition(): array{
        return [
            'name' => $this->faker->unique()->company() . ' ' . $this->faker->unique()->numberBetween(1, 1000),
            'state' => $this->faker->boolean(),
        ];
    }
}

<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeTypeFactory extends Factory{
    public function definition(): array{
        return [
            'name' => $this->faker->unique()->company() . ' ' . $this->faker->unique()->numberBetween(1, 100000),
            'state' => $this->faker->boolean(),
        ];
    }
}

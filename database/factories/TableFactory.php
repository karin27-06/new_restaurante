<?php
namespace Database\Factories;
use App\Models\Areas;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => $this->faker->word(),
            'tablenum'  => $this->faker->unique()->numerify('TBL-###'),
            'idArea'    => Areas::inRandomOrder()->first()->id,
            'idFloor'   => Floor::inRandomOrder()->first()->id,
            'capacity'  => $this->faker->numberBetween(2, 10),
            'state'     => $this->faker->boolean(80), // 80% activo
        ];
    }
}

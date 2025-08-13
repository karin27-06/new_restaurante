<?php

namespace Database\Factories;

use App\Models\Input;
use App\Models\Almacen;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InputFactory extends Factory
{
    protected $model = Input::class;

    protected static $insumos = [
        'Aceite de oliva',
        'Harina de trigo',
        'Azúcar',
        'Sal',
        'Pimienta negra',
        'Tomate',
        'Cebolla',
        'Ajo',
        'Leche',
        'Huevos',
        'Arroz',
        'Carne de res',
        'Pollo',
        'Pescado',
        'Queso',
        'Mantequilla',
        'Vino tinto',
        'Vinagre',
        'Pasta',
        'Caldo de pollo',
    ];
    protected static $unitMeasures = [
        'kg', 'g', 'litros', 'ml', 'unidad'
    ];
    public function definition()
    {
        $priceBuy = $this->faker->randomFloat(2, 1, 5); 
        $priceSale = $this->faker->randomFloat(2, $priceBuy + 1.00, 10); 
        $quantity = $this->faker->randomFloat(2, 1, 5); 

        return [
            'name' => $this->faker->unique()->randomElement(self::$insumos),
            'priceBuy' => $priceBuy,
            'priceSale' => $priceSale,
            'quantityUnitMeasure'=>$quantity,
            'idAlmacen' => Almacen::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(),
            'state' => $this->faker->boolean(90),
            'unitMeasure' => $this->faker->randomElement(self::$unitMeasures), 

        ];
    }
}

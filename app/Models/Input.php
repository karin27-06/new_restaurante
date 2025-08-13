<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priceBuy',
        'priceSale',
        'idAlmacen',
        'description',
        'state',
        'unitMeasure',
        'quantityUnitMeasure',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'idAlmacen');
    }

    // Relación muchos a muchos con Plato
    public function dishes()
    {
        return $this->belongsToMany(Dishes::class, 'dish_input', 'input_id', 'dish_id');
    }
}

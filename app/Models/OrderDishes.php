<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDishes extends Model
{
    use HasFactory;
    protected $table = 'order_dishes';
    protected $fillable = [
        'idOrder',
        'idDishes',
        'quantity',
        'state',
        'price',
    ];

    // Relación con el modelo Order
    public function order()
    {
        return $this->belongsTo(Orders::class, 'idOrder','id');
    }

    // Relación con el modelo Dish
    public function dish()
    {
        return $this->belongsTo(Dishes::class, 'idDishes','id');
    }
}

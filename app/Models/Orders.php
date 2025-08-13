<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders'; 
    protected $fillable = [
        'idCustomer',
        'idTable',
        'idUser', // Añade este campo
        'state',
    ];

    // Relación con el modelo Customer
    public function customer(): BelongsTo{
    
        return $this->belongsTo(Customer::class, 'idCustomer','id');
    }

    // Relación con el modelo Table
    public function table(): BelongsTo{
    
        return $this->belongsTo(Table::class, 'idTable','id');
    }
    // Relación con el modelo Employee

    public function User(): BelongsTo{
    
        return $this->belongsTo(User::class, 'idUser','id');
    }

    // Relación uno a muchos con OrderDish (platos en el pedido)
    public function orderDishes()
    {
        return $this->hasMany(OrderDishes::class, 'idOrder','id');
    }
}

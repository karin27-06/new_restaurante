<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'idSale',
        'idOrder',
        'subtotal',
    ];

    // Relación con Sale (Venta)
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'idSale');
    }

    // Relación con Order (Orden)
    public function order()
    {
        return $this->belongsTo(Orders::class, 'idOrder');
    }

     public function salesInvoice()
    {
        return $this->hasOne(SalesInvoice::class, 'idSale', 'idSale'); // Asegúrate de que el idSale sea el campo que conecta ambas tablas
    }
}

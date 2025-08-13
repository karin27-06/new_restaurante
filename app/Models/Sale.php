<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'idCustomer',
        'documentType',
        'paymentType',
        'operationCode',
        'stateSunat',
        'idOrder',
    ];

    // Relación con Customer (Cliente)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'idCustomer');
    }

    // Relación con SalesOrder (relación muchos a muchos entre ventas y órdenes)
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'idSale');
    }


}

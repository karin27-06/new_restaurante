<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'ruc',
        'phone',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];
    public function inputs()
    {
        return $this->hasMany(Input::class, 'idSupplier');
    }
    public function tieneRelaciones(): bool
    {
        //se agrega todas las relaciones que existan
        return $this->inputs()->exists();
    }
}

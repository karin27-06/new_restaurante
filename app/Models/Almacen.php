<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Almacen extends Model
{
    /** @use HasFactory<\Database\Factories\AlmacenFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'idAlmacen');
    }
    public function inputs()
    {
        return $this->hasMany(Input::class, 'idAlmacen');
    }
    public function tieneRelaciones(): bool
    {
        // Aquí agregas todas las relaciones que quieras chequear
        return $this->products()->exists()
            || $this->inputs()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];
    public function tables()
    {
        return $this->hasMany(Table::class, 'idFloor');
    }
    public function tieneRelaciones(): bool
    {
        // Aquí agregas todas las relaciones que quieras chequear
        return $this->tables()->exists();
    }
}

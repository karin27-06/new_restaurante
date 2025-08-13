<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Areas extends Model{
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
    ];
    public function tables()
    {
        return $this->hasMany(Table::class, 'idArea');
    }
    public function tieneRelaciones(): bool
    {
        // Aquí agregas todas las relaciones que quieras chequear
        return $this->tables()->exists();
    }
}

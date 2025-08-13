<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'idCategory');
    }
    public function dishs()
    {
        return $this->hasMany(Dishes::class, 'idCategory');
    }
    public function tieneRelaciones(): bool
    {
        return $this->dishs()->exists() || $this->products()->exists();
    }
}

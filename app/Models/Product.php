<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'name',
        'idCategory',
        'details',
        'idAlmacen',
        'state',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'idCategory');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'idAlmacen');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dishes extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'idCategory',
        'state',
    ];
    public function category(){
        return $this->belongsTo(Category::class, 'idCategory');
    }

 public function insumos()
    {
        return $this->belongsToMany(Input::class, 'dish_input', 'dish_id', 'input_id');
    }
    
}

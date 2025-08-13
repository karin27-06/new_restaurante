<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'idUsuario','numero_cajas','idVendedor','state'
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'idVendedor');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;  // Agrega esta línea para usar DB

class KardexInput extends Model
{
    use HasFactory;
    protected $table = 'kardex_inputs'; 

    protected $fillable = [
        'idUser',
        'idInput',
        'idMovementInput',
        'movement_type',
        'totalPrice',

    ];

   public function user()
{
    return $this->belongsTo(User::class, 'idUser');
}

   public function movementInput()
{
    return $this->belongsTo(MovementInput::class, 'idMovementInput');
}

   public function Input()
{
    return $this->belongsTo(Input::class, 'idInput');
}
  
public function getQuantity()
    {
        // Obtener la cantidad de detail_movements_inputs basado en idMovementInput e idInput
        return DB::table('detail_movements_inputs')
            ->where('idMovementInput', $this->idMovementInput)
            ->where('idInput', $this->idInput)
            ->sum('quantity');  // Sumamos la cantidad de todos los registros coincidentes
    }
}

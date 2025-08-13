<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementInput extends Model
{
    use HasFactory;

    // La tabla asociada al modelo
    protected $table = 'movementsInput';

    // Los atributos que son asignables en masa
    protected $fillable = [
        'code',
        'issue_date',
        'execution_date',
        'supplier_id',
        'user_id',
        'movement_type',
        'state',
        'igv_state',
        'payment_type',
    ];

    // Relaciones con otros modelos

    /**
     * Obtiene el proveedor asociado al movimiento
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Obtiene el usuario asociado al movimiento
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
public function detailMovements()
{
    return $this->hasMany(MovementInputDetail::class, 'idMovementInput');
}
public function kardexInputs()
{
    return $this->hasMany(KardexInput::class, 'idMovementInput');
}
}

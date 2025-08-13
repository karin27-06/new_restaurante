<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteCaja extends Model
{
    use HasFactory;

    // Especificar manualmente el nombre de la tabla
    protected $table = 'reporte_caja';

    protected $fillable = [
        'id_caja',
        'idVendedor',
        'monto_efectivo',
        'monto_tarjeta',
        'monto_yape',
        'monto_transferencia',
        'idUsuario',
        'fecha_ingreso',
        'fecha_salida',
        'estado_caja',
    ];

    // Relación con la tabla 'cajas'
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }
    public function vendedor()
    {
    return $this->belongsTo(User::class, 'idVendedor');
    }

}

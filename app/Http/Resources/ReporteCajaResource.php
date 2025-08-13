<?php 

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteCajaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_caja' => $this->id_caja,
            'numero_cajas' => $this->caja->numero_cajas,
            'monto_efectivo'=> floatval($this->monto_efectivo),
            'monto_tarjeta'=> floatval($this->monto_tarjeta),
            'monto_yape'=> floatval($this->monto_yape),
            'monto_transferencia'=> floatval($this->monto_transferencia),
            'idUsuario' => $this->idUsuario,
            'estado_caja' => $this->estado_caja,
            'vendedorNombre' => $this->vendedor ? $this->vendedor->name . ' ' . $this->vendedor->apellidos : 'Sin asignar',
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
            // NUEVO
            'fecha_salida' => $this->fecha_salida 
                ? \Carbon\Carbon::parse($this->fecha_salida)->format('d-m-Y H:i:s A') 
                : 'Sin cerrar',
        ];
    }
}

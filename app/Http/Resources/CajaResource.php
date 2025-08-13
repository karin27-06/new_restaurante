<?php 

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CajaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'idUsuario' => $this->idUsuario,
            'idVendedor' => $this->idVendedor,
            'numero_cajas' => $this->numero_cajas, 
            'state' => $this->state,
            'vendedorNombre' => $this->vendedor ? $this->vendedor->name . ' ' . $this->vendedor->apellidos: 'Sin asignar', // Concatenamos nombre y apellido
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
        ];
    }
}

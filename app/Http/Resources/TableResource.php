<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Orders;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Verificar si la mesa está ocupada
        $isOccupied = Orders::where('idTable', $this->id)
                            ->where('state', '!=', 'finalizado')
                            ->exists();

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'tablenum'   => $this->tablenum,
            'capacity'   => $this->capacity,
            'state'      => $this->state,
            'idArea'     => $this->idArea,
            'area_name'  => $this->area?->name,
            'idFloor'    => $this->idFloor,
            'floor_name' => $this->floor?->name,
            'creacion'   => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
            'order_status' => $isOccupied ? 'ocupado' : 'disponible', // Estado de la mesa
        ];
    }
}

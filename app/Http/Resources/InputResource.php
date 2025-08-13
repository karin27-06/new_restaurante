<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InputResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'priceBuy' => $this->priceBuy,
            'priceSale' => $this->priceSale,
            'quantityUnitMeasure'=>$this->quantityUnitMeasure,
            'idAlmacen' => $this->idAlmacen,
            'almacen_name'=>$this->almacen?->name,
            'description' => $this->description,
            'unitMeasure' => $this->unitMeasure,
            'state' => $this->state,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
        ];
    }
}

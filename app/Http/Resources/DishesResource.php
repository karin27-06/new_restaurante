<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price ?? 0,
            'quantity' => $this->quantity ?? 0,
            'idCategory' => $this->idCategory ?? 0,
            'nameCategory' => $this->category->name ?? 'Sin Categoria',
            'state' => $this->state,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
            // Agregar la relación de insumos
            'insumos' => $this->insumos->map(function($insumo) {
                return [
                    'id' => $insumo->id,
                    'name' => $insumo->name,
                    'quantityUnitMeasure' => $insumo->quantityUnitMeasure,
                    'unitMeasure' => $insumo->unitMeasure,
                ];
            }),
        ];
    }
}

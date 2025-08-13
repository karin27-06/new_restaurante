<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class OrderDishResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'idOrder' => $this->idOrder,
            'idDish'   => $this->idDishes,
            'name'   => $this->dish->name ?? null,  // Asegúrate de tener relación dish() en el modelo
            'quantity' => $this->quantity,
            'price'   => $this->price,
            'subtotal' => number_format($this->quantity * $this->price, 2),
            'state'   => $this->state,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
                        'numeroMesa' => $this->order->table->tablenum ?? null,  // Obtiene el numeroMesa de la relación con Order

        ];
    }
}

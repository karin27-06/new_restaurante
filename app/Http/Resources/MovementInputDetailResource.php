<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementInputDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'idMovementInput' => $this->idMovementInput,
            'idInput' => $this->idInput,
            'quantity' => $this->quantity,
            'totalPrice' => $this->totalPrice,
            'priceUnit' => $this->priceUnit,
            'batch' => $this->batch,
            'expirationDate' => Carbon::parse($this->expirationDate)->format('d-m-Y'),
            'movementInput' => new MovementInputResource($this->whenLoaded('movementInput')), // Relación con MovementInput
            'input' => new InputResource($this->whenLoaded('input')), // Relación con Input
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
        ];
    }
}

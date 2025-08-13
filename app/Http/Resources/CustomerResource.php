<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'codigo' => $this->codigo,
            'client_type_id' => $this->client_type_id,
            'Cliente_Tipo' => $this->clienteType->name,
            'state' => $this->state,
            'creacion' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'actualizacion' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
      ];
    }
}

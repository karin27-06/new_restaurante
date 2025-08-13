<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->customer),  // Usando un recurso para el cliente
            'documentType' => $this->documentType,
            'paymentType' => $this->paymentType,
            'operationCode' => $this->operationCode,
            'stateSunat' => $this->stateSunat,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientType extends Model
{
    /** @use HasFactory<\Database\Factories\ClientTypeFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
    ]; 
    protected $casts = [
        'state' => 'boolean',
    ];
    public function Customers():HasMany{
        return $this->hasMany(Customer::class, 'client_type_id', 'id'); 
    }
    public function tieneRelaciones(): bool
    {
        //se agrega todas las relaciones que existan
        return $this->Customers()->exists();
    }
}

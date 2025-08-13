<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'codigo',
        'client_type_id',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];

    public function clienteType(): BelongsTo{
        return $this->belongsTo(ClientType::class,'client_type_id','id');
    }
}

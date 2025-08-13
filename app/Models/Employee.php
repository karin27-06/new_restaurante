<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'codigo',
        'employee_type_id',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];

    public function empleadoType(): BelongsTo{
        return $this->belongsTo(EmployeeType::class, 'employee_type_id', 'id');
    }
}

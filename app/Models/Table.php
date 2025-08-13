<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tablenum',
        'idArea',
        'idFloor',
        'capacity',
        'state',
    ];

    // Relaciones
    public function area()
    {
        return $this->belongsTo(Areas::class, 'idArea');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'idFloor');
    }
}

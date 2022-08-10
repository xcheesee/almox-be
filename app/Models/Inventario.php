<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'item_id',
        'local_id',
        'quantidade',
        'qtd_alerta',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

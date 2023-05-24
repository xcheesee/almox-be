<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaItens extends Model
{
    use HasFactory;

    protected $fillable = [
        'transferencia_materiais_id',
        'item_id',
        'quantidade',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

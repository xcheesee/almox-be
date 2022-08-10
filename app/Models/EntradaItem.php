<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'item_id',
        'quantidade',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

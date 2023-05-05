<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaItens extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'item_id',
        'quantidade',
    ];
}

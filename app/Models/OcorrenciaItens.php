<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcorrenciaItens extends Model
{
    use HasFactory;
    protected $table = 'ocorrencia_item';
    
    protected $fillalble = [
        'ocorrencia_id',
        'item_id',
        'quantidade'
    ];

}

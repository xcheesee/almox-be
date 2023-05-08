<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ocorrencias extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_id',
        'data_ocorrencia',
        'tipo_ocorrencia',
        'boletim_ocorrencia',
        'justificativa',
        'user_id'
    ];
}

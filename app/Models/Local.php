<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    protected $fillable = [
        'departamento_id',
        'nome',
        'tipo',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'medida_id',
        'nome',
        'tipo',
        'descricao',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }
}

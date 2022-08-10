<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'local_id',
        'processo_sei',
        'numero_contrato',
        'numero_nota_fiscal',
        'arquivo_nota_fiscal',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }
}

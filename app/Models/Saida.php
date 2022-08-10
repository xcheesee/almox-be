<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saida extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'ordem_servico_id',
        'almoxarife_nome',
        'almoxarife_email',
        'almoxarife_cargo',
        'baixa_user_id',
        'baixa_datahora',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class);
    }

    public function baixa_user()
    {
        return $this->belongsTo(User::class);
    }
}

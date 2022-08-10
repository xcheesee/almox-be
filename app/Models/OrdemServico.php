<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'origem_id',
        'destino_id',
        'local_servico_id',
        'almoxarife_nome',
        'almoxarife_email',
        'almoxarife_cargo',
        'data_servico',
        'especificacao',
        'profissional',
        'horas_execucao',
        'observacoes',
        'user_id',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function origem()
    {
        return $this->belongsTo(Local::class);
    }

    public function destino()
    {
        return $this->belongsTo(Local::class);
    }

    public function local_servico()
    {
        return $this->belongsTo(Local::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

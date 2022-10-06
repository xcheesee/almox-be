<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getBaixaDatahoraFormatadaAttribute(){
        if ($this->baixa_datahora){
            $date = Carbon::parse($this->baixa_datahora);
            return $date->format("d/m/Y H:i:s");
        }

        return null;
    }
}

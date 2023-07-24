<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saida extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'ordem_servico_id',
        'local_servico_id',
        'tipo_servico_id',
        'justificativa_os',
        'especificacao',
        'observacoes',
        'baixa_user_id',
        'baixa_datahora',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function origem()
    {
        return $this->belongsTo(Local::class);
    }

    public function local_servico()
    {
        return $this->belongsTo(Local::class);
    }

    public function tipo_servico()
    {
        return $this->belongsTo(TipoServico::class);
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

    public function scopeBaixaDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('baixa_datahora', '>=', Carbon::parse($date));
    }

    public function scopeBaixaAntesDe(Builder $query, $date): Builder
    {
        return $query->where('baixa_datahora', '<=', Carbon::parse($date));
    }
}

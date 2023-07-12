<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'origem_id',
        'local_servico_id',
        'data_inicio_servico',
        'data_fim_servico',
        'almoxarife_nome',
        'almoxarife_email',
        'especificacao',
        'profissional',
        'horas_execucao',
        'observacoes',
        'flg_baixa',
        'ativo',
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

    public function getCreatedAtFormatadoAttribute(){
        if ($this->created_at){
            $date = Carbon::parse($this->created_at);
            return $date->format("d/m/Y");
        }

        return null;
    }

    public function getDataInicioFormatadaAttribute(){
        if ($this->data_inicio_servico){
            $date = Carbon::parse($this->data_inicio_servico);
            return $date->format("d/m/Y H:i:s");
        }

        return null;
    }

    public function getDataInicioFormatadaSemhoraAttribute(){
        if ($this->data_inicio_servico){
            $date = Carbon::parse($this->data_inicio_servico);
            return $date->format("d/m/Y");
        }

        return null;
    }

    public function getDataFimFormatadaAttribute(){
        if ($this->data_fim_servico){
            $date = Carbon::parse($this->data_fim_servico);
            return $date->format("d/m/Y H:i:s");
        }

        return null;
    }

    public function getDataFimFormatadaSemhoraAttribute(){
        if ($this->data_fim_servico){
            $date = Carbon::parse($this->data_fim_servico);
            return $date->format("d/m/Y");
        }

        return null;
    }

    public function scopeServicoDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('created_at', '>=', Carbon::parse($date));
    }

    public function scopeServicoAntesDe(Builder $query, $date): Builder
    {
        return $query->where('created_at', '<=', Carbon::parse($date));
    }
}

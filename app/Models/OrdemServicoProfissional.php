<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OrdemServicoProfissional extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_sevico_id',
        'profissional_id',
        'data_inicio',
        'horas_empregadas',
    ];

    protected $table = 'ordem_servico_profissionais';

    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class);
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    public function getDataInicioFormatadaAttribute(){
        if ($this->data_inicio){
            $date = Carbon::parse($this->data_inicio);
            return $date->format("d/m/Y");
        }

        return null;
    }
}

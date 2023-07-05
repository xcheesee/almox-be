<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SaidaProfissional extends Model
{
    use HasFactory;

    protected $fillable = [
        'saida_id',
        'profissional_id',
        'data_inicio',
        'horas_empregadas',
    ];

    protected $table = 'saida_profissionais';

    public function saida()
    {
        return $this->belongsTo(Saida::class);
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

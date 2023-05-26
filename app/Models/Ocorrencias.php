<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ocorrencias extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_id',
        'data_ocorrencia',
        'tipo_ocorrencia',
        'boletim_ocorrencia',
        'justificativa',
        'user_id'
    ];

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function scopeOcorrenciaDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('data_ocorrencia', '>=', Carbon::parse($date));
    }

    public function scopeOcorrenciaAntesDe(Builder $query, $date): Builder
    {
        return $query->where('data_ocorrencia', '<=', Carbon::parse($date));
    }
}

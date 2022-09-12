<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'local_id',
        'data_entrada',
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

    public function scopeEntradaDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('data_entrada', '>=', Carbon::parse($date));
    }

    public function scopeEntradaAntesDe(Builder $query, $date): Builder
    {
        return $query->where('data_entrada', '<=', Carbon::parse($date));
    }
}

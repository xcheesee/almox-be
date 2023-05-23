<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class TransferenciaDeMateriais extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_origem_id',
        'base_destino_id',
        'data_transferencia',
        'status',
        'user_id',
        'observacao',
        'observacao_motivo',
        'observacao_user_id',
    ];

    public function base_origem_id()
    {
        return $this->belongsTo(Local::class, 'base_origem_id');
    }

    public function base_destino_id()
    {
        return $this->belongsTo(Local::class, 'base_destino_id');
    }

    public function itens_da_transferencia()
    {
        return $this->hasMany(TransferenciaItens::class, 'transferencia_materiais_id');
    }

    public function scopeTransferenciaDepoisDe(Builder $query, $date): Builder
    {
        return $query->where('data_transferencia', '>=', Carbon::parse($date));
    }

    public function scopeTransferenciaAntesDe(Builder $query, $date): Builder
    {
        return $query->where('data_transferencia', '<=', Carbon::parse($date));
    }
}

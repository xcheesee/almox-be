<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

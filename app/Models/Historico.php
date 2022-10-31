<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_tabela',
        'data_acao',
        'tipo_acao',
        'user_id',
    ];

    protected $table = 'historicos';

    public function userHistorico()
    {
        return $this->belongsTo(User::class);
    }
}

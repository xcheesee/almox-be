<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Kyslik\ColumnSortable\Sortable;

class Historico extends Model
{
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'nome_tabela',
        'data_acao',
        'tipo_acao',
        'user_id',
        'registro'
    ];

    public $sortable = ['id','data_acao','nome_tabela','tipo_acao'];
    protected $table = 'historicos';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDataAcaoFormatadaAttribute(){
        if ($this->data_acao){
            $date = Carbon::parse($this->data_acao);
            return $date->format("d/m/Y");
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TipoServico extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','servico'];

    protected $fillable = [
        'departamento_id',
        'servico'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}

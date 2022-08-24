<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TipoItem extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','nome'];

    protected $fillable = [
        'departamento_id',
        'nome'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}

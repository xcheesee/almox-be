<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Item extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','nome'];

    protected $fillable = [
        'departamento_id',
        'medida_id',
        'tipo_item_id',
        'nome',
        'descricao',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }

    public function tipo_item()
    {
        return $this->belongsTo(TipoItem::class);
    }
}

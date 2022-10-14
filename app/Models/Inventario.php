<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Inventario extends Model
{
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'departamento_id',
        'item_id',
        'local_id',
        'quantidade',
        'qtd_alerta',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeQuantidadeMaiorQue(Builder $query, $val): Builder
    {
        return $query->where('quantidade', '>=', $val);
    }

    public function scopeQuantidadeMenorQue(Builder $query, $val): Builder
    {
        return $query->where('quantidade', '<=', $val);
    }
}

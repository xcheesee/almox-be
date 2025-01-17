<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Departamento extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','nome','andar','ativo'];

    protected $fillable = [
        'nome',
        'andar',
        'ativo',
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Medida extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','tipo'];

    protected $fillable = [
        'tipo',
    ];
}

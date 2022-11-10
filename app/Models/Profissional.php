<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Profissional extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = ['id','nome','profissao'];

    protected $fillable = [
        'departamento_id',
        'local_id',
        'nome',
        'profissao'
    ];

    protected $table = 'profissionais';

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function getCompletoAttribute(){
        return $this->nome.' ('.$this->profissao.')';
    }
}

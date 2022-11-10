<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsaveisEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'nome',
        'email',
        'ativo'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartamentoUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_id',
        'user_id'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

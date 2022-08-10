<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServicoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_sevico_id',
        'item_id',
        'quantidade',
    ];

    public function ordem_servico()
    {
        return $this->belongsTo(OrdemServico::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

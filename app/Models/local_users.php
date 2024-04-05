<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class local_users extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_id',
        'user_id',
    ];

    public function local()
    {
        return $this->belongsTo(Local::class);
    }
}

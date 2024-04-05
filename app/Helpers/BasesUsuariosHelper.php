<?php

namespace App\Helpers;

use App\Models\local_users;

class BasesUsuariosHelper
{
    public static function ExibirBasesUsuarios($id)
    {
        $bases = local_users::where('user_id', $id)
        ->with('local')->get();

        return $bases->pluck('local');
    }

    public static function ExibirIdsBasesUsuarios($id)
    {
        $bases = local_users::where('user_id', $id)
        ->with('local')->get();

        $ids = array();

        foreach($bases as $base){
            array_push($ids, $base->local_id);
        }

        return $ids;
    }
}
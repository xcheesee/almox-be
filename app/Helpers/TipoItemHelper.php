<?php

namespace App\Helpers;

use App\Models\TipoItem;

class TipoItemHelper
{
    public static function dropDownList($departamentos){
        $locais = TipoItem::query()
            ->whereIn('departamento_id',$departamentos)
            ->orderBy('nome')
            ->get();
        $arrSelect = array();

        foreach($locais as $k=>$v){
            $arrSelect[$v->id] = $v->nome;
        }

        return $arrSelect;
    }
}

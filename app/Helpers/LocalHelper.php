<?php

namespace App\Helpers;

use App\Models\Local;

class LocalHelper
{
    public static function dropDownList($departamentos,$tipo = null){
        $locais = Local::query()
            ->whereIn('departamento_id',$departamentos)
            ->when($tipo, function ($query, $val) {
                return $query->where('tipo','=',$val);
            })
            ->orderBy('nome')
            ->get();
        $arrSelect = array();

        foreach($locais as $k=>$v){
            $arrSelect[$v->id] = $v->nome;
        }

        return $arrSelect;
    }
}

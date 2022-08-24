<?php

namespace App\Helpers;

use App\Models\Subprefeitura;

class PrefeituraHelper
{
    // public static function subprefeituraDropdown(){
    //     $deptos = Subprefeitura::query()->orderBy('nome')->get(); //->where('ativo','=',1)

    //     $arr = array();
    //     foreach($deptos as $k=>$v){
    //         $arr[$v->id] = $v->nome.' ('.$v->regiao.')';
    //     }

    //     return $arr;
    // }

    public static function regiaoDropdown(){
        $arr = array('CO'=>'Centro-Oeste','L'=>'Leste','N'=>'Norte','S'=>'Sul');
        return $arr;
    }
}

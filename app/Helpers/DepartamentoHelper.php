<?php

namespace App\Helpers;

use App\Models\Departamento;
use App\Models\DepartamentoUsuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DepartamentoHelper
{
    public static function dropDownList(){
        $departamentos = Departamento::query()->orderBy('nome')->get(); //->where('ativo','=',1)

        $arrSelect = array();
        foreach($departamentos as $k=>$v){
            $arrSelect[$v->id] = $v->nome;
        }

        return $arrSelect;
    }

    public static function deptosByUser($user, string $coluna, $bypass_admin = true){
        if ($user->hasRole('admin') && $bypass_admin){
            $departamentos = Departamento::query()->orderBy('id')->get(); //->where('ativo','=',1)
            $arrSelect = array();
            foreach($departamentos as $v){
                $arrSelect[$v->id] = $v[$coluna];
            }
        }else{
            $departamentos = DepartamentoUsuario::query()->where('user_id','=',$user->id)->orderBy('departamento_id')->get(); //->where('ativo','=',1)
            $arrSelect = array();
            foreach($departamentos as $v){
                $arrSelect[$v->departamento->id] = $v->departamento[$coluna];
            }
        }

        return $arrSelect;
    }

    public static function ids_deptos($user){
        $arrSelect = array();
        if ($user->hasRole('admin')){
            $departamentos = Departamento::query()->orderBy('id')->get(); //->where('ativo','=',1)
            foreach($departamentos as $v){
                $arrSelect[] = $v->id;
            }
        }else{
            $departamentos = DepartamentoUsuario::query()->where('user_id','=',$user->id)->orderBy('departamento_id')->get(); //->where('ativo','=',1)
            foreach($departamentos as $v){
                $arrSelect[] = $v->departamento_id;
            }
        }

        return $arrSelect;
    }
}

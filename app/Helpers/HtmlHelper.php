<?php

namespace App\Helpers;

//use App\Models\Subprefeitura;

class HtmlHelper
{
    public static function converteDatetimeLocal2MySQL($datetime){
        if($datetime){
            return str_replace('T',' ',$datetime).':00';
        }
        return null;
    }
}

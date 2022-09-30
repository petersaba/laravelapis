<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Flysystem\CorruptedPathDetected;

class GeneralController extends Controller
{
    function firstApi(Request $request){
        $given_str = $request->data;
        $lower_case = [];
        $upper_case = [];
        $numbers = [];
        $str_arr = str_split($given_str);


        foreach ($str_arr as $value){
            $ascii = ord($value);
            if($ascii >= 48 && $ascii <= 57){
                $numbers[] = $value;
            }else if($ascii >= 65 && $ascii <= 90){
                $upper_case[] = $value; 
            }else if($ascii >= 97 && $ascii <= 122){
                $lower_case[] = $value;
            }      
        }

        sort($lower_case);
        sort($upper_case);
        sort($numbers);

    }
}

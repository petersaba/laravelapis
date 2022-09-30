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
            $ascii = ord($value); // getting the ascii code of the current char
            if($ascii >= 48 && $ascii <= 57){ // numbers have ascii codes between 48 and 57
                $numbers[] = $value;
            }else if($ascii >= 65 && $ascii <= 90){ // upper case characters have ascii codes between 65 and 90
                $upper_case[] = $value; 
            }else if($ascii >= 97 && $ascii <= 122){ // lower case characters have ascii codes between 97 and 122
                $lower_case[] = $value;
            }      
        }

        sort($lower_case);
        sort($upper_case);
        sort($numbers);

        $i = 0;
        $j = 0;
        $result = [];

        // inspired from the merge algorithm
        while(isset($upper_case[$i]) && isset($lower_case[$j])){
            $upper_case_ascii = ord($upper_case[$i]);
            $lower_case_ascii = ord($lower_case[$j]);
            // the ascii codes of lower case characters are the same as the upper case character code + 32
            if($upper_case_ascii >= $lower_case_ascii - 32){
                $result[] = $lower_case[$j++];
            }else {
                $result[] = $upper_case[$i++];
            }
        }

        // if all lower cases are added and there is still more upper cases
        while(isset($upper_case[$i])){
            $result[] = $upper_case[$i++];
        }
        
        // if all upper cases are added and there is still more lower cases
        while(isset($lower_case[$j])){
            $result[] = $lower_case[$j++];
        }

        // add the sorted number array at the end
        $result = array_merge($result, $numbers);
        $result = implode($result);

        return response() -> json([
            'status' => 'success',
            'message' => $result
        ]);
    }
}

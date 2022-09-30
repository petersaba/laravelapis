<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Flysystem\CorruptedPathDetected;

class GeneralController extends Controller
{
    // first API
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
        sort($numbers);

        $mergedCharacters = self::mergeCharacters($lower_case, $upper_case);

        // add the sorted number array at the end
        $result = array_merge($mergedCharacters, $numbers);
        $result = implode($result);

        return response() -> json([
            'status' => 'success',
            'message' => $result
        ]);
    }

    // inspired from the merge algorithm
    function mergeCharacters($lower_case, $upper_case){
        sort($lower_case);
        sort($upper_case);

        $i = 0;
        $j = 0;
        $result = [];

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

        return $result;
    }

    // second API
    function secondApi(Request $request){
        $number = $request->number;

        // casting number to int will return 0 for the value 0 and for a non number value
        if($number != '0' && (int)$number == 0){
            return response() -> json([
                'status' => 'fail',
                'message' => 'passed value is not a number'
            ]);
        }else{
            $number_arr = str_split($number);
            $negative = $number_arr[0] == '-' ? TRUE : FALSE;
            if($negative){
                array_splice($number_arr, 0, 1); // remove the '-' from the array 
                $result = self::fillNegativeNumbers($number_arr);
            }else{
                $result = self::fillPorsitiveNumbers($number_arr);
            }

            return response() -> json([
                'status' => 'success',
                'message' => $result
            ]);
        }
    }

    function fillNegativeNumbers($number_arr){
        $result = [];

        for($i = 0; $i<count($number_arr); $i++)
            $result[] = -$number_arr[$i] * (10**(count($number_arr) - $i-1));

        return $result;
    }

    function fillPorsitiveNumbers($number_arr){
        $result = [];

        for($i = 0; $i<count($number_arr); $i++)
            $result[] = $number_arr[$i] * (10**(count($number_arr) - $i-1));

        return $result;
    }

    // third API
    function thirdApi(Request $request){
        $sentence = $request->sentence;

        $sentence = str_split($sentence);
        $found_int = FALSE;
        $found_int_at = NULL;
        $found_ints = [];
        for($i =0; $i<count($sentence); $i++){
            if((int)$sentence[$i] != '0' || $sentence[$i] == '0'){
                if(!$found_int){
                    $found_int = TRUE;
                    $found_int_at = $i;
                }
            }else{
                if($found_int){
                    $found_int = FALSE;
                    $found_ints[] = [$found_int_at, $i -1];
                    $found_int_at = NULL;
                }
            }
        }
        
        return response() -> json([
            'status' => 'success',
            'message' => $found_ints
        ]);
        
    }
    
}
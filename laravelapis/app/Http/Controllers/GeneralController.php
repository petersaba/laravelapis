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
        $sentence_arr = str_split($sentence);

        $found_ints_interval = self::getAllNumberIntervals($sentence_arr);

        $numbers_arr = self::getAllNumbers($found_ints_interval, $sentence);

        $binary_numbers = self::turnIntoBinary($numbers_arr);

        $sentence_arr = self::placeBinaryInArray($binary_numbers, $found_ints_interval, $sentence_arr);

        $sentence = implode($sentence_arr);

        return response() -> json([
            'status' => 'success',
            'message' => $sentence
        ]);
    }

    // get the start index and the end index of each number inside the sentence and storing them in an array
    function getAllNumberIntervals($sentence_arr){
        $found_int = FALSE;
        $found_int_at = NULL;
        $found_ints_interval = [];
        for($i =0; $i<count($sentence_arr); $i++){
            if((int)$sentence_arr[$i] != '0' || $sentence_arr[$i] == '0'){
                if(!$found_int){
                    $found_int = TRUE;
                    $found_int_at = $i;
                }
            }else{
                if($found_int){
                    $found_int = FALSE;
                    $found_ints_interval[] = [$found_int_at, $i -1];
                    $found_int_at = NULL;
                }
            }
        }
        return $found_ints_interval;
    }

    // get the actual numbers in the sentence based on the start and end index stored in the intervals array
    function getAllNumbers($found_ints_interval, $sentence){
        $numbers_arr = [];
        foreach($found_ints_interval as $value){
            $number = '';
            for($i=$value[0]; $i<=$value[1]; $i++){
                $number .= $sentence[$i];
            }
            $numbers_arr[] = $number;
        }
        return $numbers_arr;
    }

    // turn all found numbers to binary
    function turnIntoBinary($numbers_arr){
        for($i=0; $i<count($numbers_arr); $i++){
            $numbers_arr[$i] = decbin($numbers_arr[$i]);
        }
        return $numbers_arr;
    }

    // replace the numbers by their binary numbers inside the sentence array
    function placeBinaryInArray($binary_numbers, $found_ints_interval, $sentence_arr){
        for($key=0; $key<count($found_ints_interval); $key++){
            $value = $found_ints_interval[$key];
            // the difference in the length of the array and the indexes of elements as multiple elements will be replaced by one element which is the binary number
            $difference = $value[1] - $value[0]; 
            array_splice($sentence_arr, $value[0], $difference +1, $binary_numbers[$key]); 
            // changing the indexes of the upcoming intervals based on the difference of the previous changes to the array
            for($i=$key+1; $i<count($found_ints_interval); $i++){
                $found_ints_interval[$i][0] -= $difference;
                $found_ints_interval[$i][1] -= $difference;
            } 
        }
        return $sentence_arr;
    }
}
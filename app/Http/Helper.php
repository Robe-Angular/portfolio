<?php
namespace App\Http;
use Illuminate\Http\Request;

class Helper{
    public static function get_params(Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        return $params;
    }
}
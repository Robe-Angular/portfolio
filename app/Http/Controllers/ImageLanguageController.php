<?php

namespace App\Http\Controllers;

use App\Models\Image_language;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageLanguageController extends Controller
{
    public function __construct(){
        $this->middleware('assign.guard:admin',['except' => [
            'getImageByDescription'
        ]]);
    }

    public function listByImage($image_id){
        $images_language = Image_language::where('image_id',$image_id)->get();
        return response()->json(['images_language' => $images_language],200);
    }

    public function edit($image_language_id, Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'data empty'
        ];
        
        if(!empty($params_array)){
            $validate = \Validator::make($params_array, [
                'description_language' => 'required'
            ]);
            
            if(!$validate->fails()){
                
                $image_language = Image_language::find($image_language_id);
                $data = [
                    'code' => 404
                ];
                if($image_language){
                    $image_language->description_language = $params->description_language;
                    $image_language_updated = $image_language->save();
                    if($image_language_updated){
                        return response()->json([
                            'image_language_updated' => $image_language_updated
                        ],200);
                    }
                    
                }
                
            }
        }
        return response()->json($data,$data['code']);
    }
    public function getImageByDescription($description_language){
        $image = Image::whereHas('imagesLanguage', function ($query) use ($description_language) {
            $query->where('description_language', $description_language);
        })->first();
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'data empty'
        ];
        
        if($image && $image->count() > 0){
            $file = $image->image_name;
            if(Storage::exists($file)){
                return response()->file(Storage::path($file));
            }
            
        }
        return response()->json($data,$data['code']);
    }
}

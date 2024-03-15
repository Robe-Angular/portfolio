<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Project;

class ImageController extends Controller
{
    public function __construct(){
        $this->middleware('assign.guard:admin',['except' => [
            'getImage'
        ]]);
    }
    public function upload(Request $request,$project_id){
        $project = Project::find($project_id);
        $data=[
            'code'=>400
        ];
        if($project){
            $file = $request->file("file0");
            $validate = \Validator::make($request->all(), [
                'file0' => 'required|mimes:jpg,jpeg,png,gif'
            ]);
            if(!$validate->fails()){
                $path = $file->store();
                if($path){
                    $image = new Image();
                    $image->project_id = $project_id;
                    $image->image_name = $path;
                    $image->save();
                    return response()->json(['path',$path],200);
                }
            }
        }
        return response()->json($data,$data['code']);
    }

    public function destroy($project_id){
        
    }
}

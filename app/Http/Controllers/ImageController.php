<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

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

    public function destroy($image_id){
        
        $image=Image::find($image_id);
        $data = [
            'code'=> 404
        ];
        if($image){
            $image_name = $image->image_name;
            $data = [
                'code'=> 409,
                'message' => 'can\'t delete'

            ];
            if(!Project::where('image_id',$image_id)->exists()){
                Storage::disk('local')->delete($image_name);
                $deleted = $image->delete();
                return response()->json(['deleted'=>$deleted],200);
            }

            
        }
        return response()->json($data,$data['code']);
    }

    public function imagesByPost($project_id){
        $images=Image::where('project_id',$project_id)->get();
        return response()->json([
            'images'=> $images
        ],200);
    }

    public function setMain($image_id){
        $image=Image::find($image_id);
        $project = Project::find($image->project_id);
        $data = [
            'code'=> 400
        ];
        if(!$project->image_id == $image_id){
            $project->image_id = $image_id;
            $project->save();
            return response()->json(['code'=>200],200);
        }
        return response()-> json($data,$data['code']);
    }
}

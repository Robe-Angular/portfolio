<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Technology;
use App\Models\Project_technology;

class TechnologyController extends Controller
{
    public function __construct(){
        $this->middleware('assign.guard:admin',['except' => []]);
    }

    public function create(Request $request){
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
                'name' => 'required'
            ]);
            
            if(!$validate->fails()){
                $technology = new Technology();
                $technology->name = $params_array['name'];
                $technology->save();
                return response()->json(['technology'=>$technology],200);
            }
        }
        return response()->json($data,$data['code']);
    }

    public function getProjectsByTechnology($technology_id){

        $technology = Technology::find($technology_id);
        $data = [
            'code'=> 404
        ];

        if($technology){
            $projects = $technology->projects;
            return response()->json(['projects'=>$projects],200);
        }
        return response()->json($data,$data['code']);
        
    }


    public function getTechnologiesByProject($project_id){
        $project = Project::find($project_id);
        $data = [
            'code'=> 404
        ];
        if($project){
            $technologies = $project->technologies;
            return response()->json(['technologies'=>$technologies],200);
        }
        return response()->json($data,$data['code']);
    }

    public function addTechnologyToProject($project_id,$technology_id){
        $data=[
            'code'=>404
        ];
        $project = Project::find($project_id);
        $technology = Technology::find($technology_id);
        if($project && $technology){
            $is_technology = $project->technologies()->find($technology_id);
            $data=[
                'code'=>403
            ];  
            if(!$is_technology){
                $project->technologies()->attach($technology_id);
                return response()->json([
                    'message'=>'attached'
                ],200);
            }
        }
        return response()->json($data,$data['code']);

    }

    public function removeTechnologyFromProject($project_id,$technology_id){
        $data=[
            'code'=>404
        ];
        $project = Project::find($project_id);
        $technology = Technology::find($technology_id);
        if($project && $technology){
            $is_technology = $project->technologies()->find($technology_id);
            if($is_technology){
                $project->technologies()->detach($technology_id);
                return response()->json([
                    'message'=>'detached'
                ],200);
            }
        }
        return response()->json($data,$data['code']);
    }

    public function destroy($technology_id){
        $technology = Technology::find($technology_id);
        $data=[
            'code'=>404
        ];
        if($technology){
            $technology->delete();
            return response()->json(['message'=>'deleted'],200);
        }
        return response()->json($data,$data['code']);
    }

    public function getTechnologiesLike($string_like){
        $technologies = Technology::where('name','LIKE','%'.$string_like.'%')->get();
        return response()->json(['technologies'=>$technologies],200);
    }

}

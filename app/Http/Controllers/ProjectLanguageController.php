<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project_language;

class ProjectLanguageController extends Controller
{
    public function __construct(){
        $this->middleware('auth:admin',['except' => ['detail']]);
    }

    public function detail($project_language_id){
        $project_language = Project_language::find($project_language_id);
        $data = [
            'code'=>404
        ];
        if($project_language){
            return response()->json([
                'project_language'=>$project_language
            ],200);
        }
        return response()->json($data,$data['code']);
    }

    public function update(Request $request, $project_language_id){
        $project_language = Project_language::find($project_language_id);
        $data = [
            'code'=> 404
        ];
        if($project_language){
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            if(!empty($params_array)){
                $validate = \Validator::make($params_array, [
                    'title_language' => 'required',
                    'content_language' => 'required'
                ]);
                $data = [
                    'code'=> 400
                ];
                if(!$validate->fails()){
                    $project_language->title_language = $params_array['title_language'];
                    $project_language->content_language = $params_array['content_language'];
                    $project_language_updated = $project_language->save();
                    if($project_language_updated){
                        return response()->json([
                            'project_language_updated'=>$project_language_updated
                        ],200);
                    }
                    
                }
            }
        }
        return response()->json($data,$data['code']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Project_language;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(){
        $this->middleware('assign.guard:admin',['except' => ['detail','index']]);
    }

    public function index(){
        $projects = Project::all();
        return response()->json([
            'projects' => $projects
        ]);
    }

    public function store(Request $request) {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                        'name' => 'required',
                        'url' => 'required',
                        'confidential' => 'required'
            ]);
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'faltan datos'
                ];
            } else {
                $project = new Project();
                $project->name = $params->name;
                $project->url = $params->url;
                $project->confidential = $params->confidential;
                $project->save();
                
                $config_langs = config('static_arrays.languages');
                foreach ($config_langs as $lang) {
                    $project_language = new Project_language();
                    $project_language->project_id = $project->id;
                    $project_language->language = $lang;
                    $project_language->title_language = "";
                    $project_language->content_language = "";
                    $project_language->save();
                }
                
                
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'project' => $project
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Send correct data'
            ];
        }
        return response()->json($data, $data['code']);
    }
 
    public function edit($project_id,Request $request) {
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
                'name' => 'required',
                'url' => 'required',
                'confidential' => 'required'
            ]);
            if(!$validate->fails()){
                
                $project_to_update = Project::find($project_id);
                $data = [
                    'code' => 404
                ];
                if($project_to_update){
                    $project_to_update->name = $params->name;
                    $project_to_update->url = $params->url;
                    $project_to_update->confidential = $params->confidential;
                    $project_updated = $project_to_update->save();
                    if($project_updated){
                        return response()->json([
                            'project_updated' => $project_updated
                        ],200);
                    }
                    
                }
                
            }
        }
        
        return response()->json($data,$data['code']);
    }

    public function detail($project_id){
        $project = Project::find($project_id);
        return response()->json([
            'project' => $project
        ]);
    }

    public function delete($project_id){
        $project = Project::find($project_id);
        $data = [
            'code' => 404
        ];
        if($project){
            $project_delete = $project->delete();
            return response()->json([
                'deleted' => $project_delete
            ],200);

        }
        return response()->json($data,$data['code']);
    }

    private function getIdentity($request){
        return response()->json(auth()->user());
    }
}
    

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public function MakeAdminAndLogin(){
        $response = $this->get('/api/make-admin');
        $response = $this->postJson('/api/login',['json'=>
            json_encode([
                "email"=>"admin@admin.admin",
                "password"=>"LaravelJWT"
            ])
        ]);
        return $response;
    }

    public function GetToken(){
        $response = $this->MakeAdminAndLogin();
        $array_response = $response->getOriginalContent();
        $jwt_token = $array_response['token'];
        return $jwt_token;
    }

    public function TryPostTestProject($jwt_token,$project_name,$confidential){
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->postJson('/api/project',['json' =>
            json_encode([
                "name"=>$project_name,
                "url"=>$project_name.".com",
                "confidential"=>$confidential
            ])
        ]);
        return $response;
    }

    

}

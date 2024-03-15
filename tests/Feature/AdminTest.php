<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_make_admin_login(): void
    {
        $response = $this->MakeAdminAndLogin();

        $response->assertJsonStructure([
            'token','user_role'
        ]);
        $response->assertJsonPath('user_role','admin');
        $response->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'token' => 'string',
                'user_role' => 'string'
            ])
        );
        
    }

    public function test_make_admin_logout_with_post_project(){
        $jwt_token = $this->GetToken();

        $response = $this->TryPostTestProject($jwt_token,"portfolio",false);
        $response->assertJsonStructure([
            'code','status','project'
        ]);
        
        $response->assertJsonPath('project.name','portfolio');
        $response->assertJsonPath('project.confidential',false);

        $response = $this->TryPostTestProject($jwt_token,"portfolio1",true);
        $response->assertJsonPath('project.name','portfolio1');
        $response->assertJsonPath('project.url','portfolio1.com');
        $response->assertJsonPath('project.confidential',true);
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->postJson('/api/logout',['json' =>
            json_encode([
            ])
        ]);

        $response->assertJsonPath('message','logout');

        $response = $this->TryPostTestProject($jwt_token,"portfolio2",false);
        $response->assertJsonPath('error','Unauthorized');
    }

    public function test_return_info_after_login(){
        $jwt_token = $this->GetToken();
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->get('/api/admin/info');
        
        $response->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
            'info.id' => 'integer',
            'info.name' => 'string',
            'info.email' => 'string',
            'info.created_at' => 'string',
            'info.updated_at' => 'string',
            'info.email_verified_at' => 'null'
        ])
    );

    }

   

}

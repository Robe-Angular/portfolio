<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectLanguageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_detail_delete_on_cascade():void
    {
        $jwt_token = $this->GetToken();
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $response = $this->getJson('api/project-language/1');
        $response->assertJsonPath('project_language.id',1);
        
        $response = $this->withHeaders([
            'Authorization'=>'Bearer '.$jwt_token
        ])
        ->deleteJson('api/project/1');
        $response = $this->getJson('api/project-language/1');
        $response->assertJsonPath('code',404);
    }
    public function test_update():void
    {
        $jwt_token = $this->GetToken();
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $response = $this->getJson('api/project-language/4');
        $response->assertJsonPath('project_language.id',4);
        $response->assertJsonPath('project_language.title_language','');
        $response->assertJsonPath('project_language.content_language','');
        $response = $this
            ->withHeader('Authorization','Bearer '.$jwt_token)
            ->putJson('api/project-language/4',['json' =>
                json_encode([
                    'title_language' => 'new title',
                    'content_language' => 'new content'
                ])
        ]);
        $response->assertStatus(200);
        $response = $this->getJson('api/project-language/4');
        $response->assertJsonPath('project_language.id',4);
        $response->assertJsonPath('project_language.title_language','new title');
        $response->assertJsonPath('project_language.content_language','new content');
        $response = $this
            ->withHeader('Authorization','Bearer '.$jwt_token)
            ->putJson('api/project-language/7',['json' =>
                json_encode([
                    'title_language' => 'new title',
                    'content_language' => 'new content'
                ])
        ]);
        $response->assertStatus(404);
    }
}

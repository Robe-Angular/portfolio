<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_update_detail_project_running_parallel(): void
    {
        $jwt_token = $this->GetToken();
        $response = $this->TryPostTestProject($jwt_token,'portfolio',true);
        $response_content = $response->GetOriginalContent();
        $response->assertJsonPath('project.id',1);

        $response = $this->getJson('api/project/1');
        $response->assertJsonPath('project.id',1);
        $response->assertJsonPath('project.name','portfolio');
        $response->assertJsonPath('project.url','portfolio.com');
        $response->assertJsonPath('project.confidential',1);
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->putJson('/api/project/'.$response_content['project']->id,['json' =>
            json_encode([
                'name' => 'changed',
                'url' => 'changed.changed',
                'confidential' => false
            ])
        ]);
        $response->assertJsonPath('project_updated',true);

        $response = $this->getJson('api/project/1');
        $response->assertJsonPath('project.id',1);
        $response->assertJsonPath('project.name','changed');
        $response->assertJsonPath('project.url','changed.changed');
        $response->assertJsonPath('project.confidential',0);
        
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->putJson('/api/project/'.'7',['json' =>
            json_encode([
                'name' => 'changed',
                'url' => 'changed.changed',
                'confidential' => false
            ])
        ]);
        $response->assertJsonPath('code',404);

        $response = $this->getJson('api/project/1');
        $response->assertJsonPath('project.id',1);
        $response->assertJsonPath('project.name','changed');
        $response->assertJsonPath('project.url','changed.changed');
        $response->assertJsonPath('project.confidential',0);

        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->putJson('/api/project/'.'7',['json' =>
            json_encode([
            ])
        ]);
        $response->assertJsonPath('code',400);

        
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->postJson('/api/logout',['json' =>
            json_encode([
            ])
        ]);
        $response = $this->getJson('api/project/1');
        $response->assertJsonPath('project.id',1);
        $response->assertJsonPath('project.name','changed');
        $response->assertJsonPath('project.url','changed.changed');
        $response->assertJsonPath('project.confidential',0);

        $response = $this->getJson('api/project/7');
        $response->assertJsonMissingPath('project.id');
        $response->assertJsonMissingPath('project.name');
        $response->assertJsonMissingPath('project.url');
        $response->assertJsonMissingPath('project.confidential');
    }

    public function test_index_projects(): void
    {
        $jwt_token = $this->GetToken();

        for($i = 1;$i < 11; $i++){
            $this->TryPostTestProject($jwt_token,'portfolio'.$i,true);
        }

        $response = $this->getJson('api/project');
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has('projects',10)
            ->has('projects.9',fn(AssertableJson $json) =>
                $json->where('id',11)->where('name','portfolio10')->where('url','portfolio10.com')->etc()
            )->has('projects.8',fn(AssertableJson $json) =>
                $json->where('id',10)->where('name','portfolio9')->where('url','portfolio9.com')
                ->where('confidential',1)->etc()
            )
        );

        for($i = 11;$i < 15; $i++){
            $this->TryPostTestProject($jwt_token,'portfolio'.$i,false);
        }

        $this->withHeaders([
            "Authorization" => "Bearer ".$jwt_token
        ])->postJson('/api/logout',['json' =>
            json_encode([
            ])
        ]);
        $response = $this->getJson('api/project');
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has('projects',14)
            ->has('projects.10',fn(AssertableJson $json) =>
                $json->where('id',12)->where('name','portfolio11')->where('url','portfolio11.com')->etc()
            )->has('projects.13',fn(AssertableJson $json) =>
                $json->where('id',15)->where('name','portfolio14')->where('url','portfolio14.com')
                ->where('confidential',0)->etc()
            )
        );
    }

    public function test_delete_project(){
        $jwt_token = $this->GetToken();
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $this->getJson('api/project');
        $response = $this->withHeaders([
            'Authorization'=>'Bearer '.$jwt_token
        ])
        ->deleteJson('api/project/16');
        
        $response->assertJsonPath('deleted',true);
        $response = $this->getJson('api/project');
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has('projects',0)
        );
        $response = $this->withHeaders([
            'Authorization'=>'Bearer '.$jwt_token
        ])
        ->deleteJson('api/project/16');
        $response->assertJsonPath('code',404);
    }
}

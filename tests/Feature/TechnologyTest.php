<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TechnologyTest extends TestCase
{
    use RefreshDatabase;
    public function test_technology_add_remove_get_projects_get_technologies(): void
    {
        $jwt_token = $this->GetToken();
        
        $response = $this->withHeader('Authorization','Bearer '.$jwt_token)
            ->postJson("api/technology",['json'=>json_encode(
                [
                    'name'=> 'PHP'
        ])]);
        $response->assertStatus(200);
        $response = $this->postJson("api/technology",['json'=>json_encode(
            [
                'name'=> 'PHP'
        ])]);
        $response->assertStatus(500);
        $technologies = ['Angular','Laravel','Git', 'Docker', 'React', 'Django', 'Flask', 'Symfony'];
        foreach ($technologies as $technology) {
            $this->postJson("api/technology",['json'=>json_encode(
                [
                    'name'=> $technology
            ])]);
        }
        $this->TryPostTestProject($jwt_token, 'Portfolio', false);
        $this->getJson('api/add-technology/1/1');
        $this->getJson('api/add-technology/1/3');
        $this->getJson('api/add-technology/1/4');
        $this->getJson('api/add-technology/1/5');
        $response = $this->getJson('api/technologies-by-project/1');
        $response->assertJson(fn(AssertableJson $json) => $json->has('technologies',4)->has(
            'technologies.0',fn(AssertableJson $json) => $json->where('name','PHP')->etc()
        )->has(
            'technologies.1',fn(AssertableJson $json) => $json->where('name','Angular')->etc()
        )->has(
            'technologies.2',fn(AssertableJson $json) => $json->where('name','Laravel')->etc()
        )->has(
            'technologies.3',fn(AssertableJson $json) => $json->where('name','Git')->etc()
        ));
        $this->getJson('api/remove-technology/1/3');
        $response = $this->getJson('api/technologies-by-project/1');
        $response->assertJson(fn(AssertableJson $json) => $json->has('technologies',3)->has(
            'technologies.0',fn(AssertableJson $json) => $json->where('name','PHP')->etc()
        )->has(
            'technologies.1',fn(AssertableJson $json) => $json->where('name','Laravel')->etc()
        )->has(
            'technologies.2',fn(AssertableJson $json) => $json->where('name','Git')->etc()
        ));
        $this->deleteJson('api/technology/4');
        $response = $this->getJson('api/technologies-by-project/1');
        
        $response->assertJson(fn(AssertableJson $json) => $json->has('technologies',2)->has(
            'technologies.0',fn(AssertableJson $json) => $json->where('name','PHP')->etc()
        )->has(
            'technologies.1',fn(AssertableJson $json) => $json->where('name','Git')->etc()
        ));
        $this->TryPostTestProject($jwt_token, 'Portfolio1', false);
        $this->getJson('api/add-technology/2/4');
        $this->getJson('api/add-technology/2/5');
        $this->getJson('api/add-technology/2/6');
        $this->getJson('api/add-technology/2/7');

        $response = $this->getJson('api/projects-by-technology/1');
        $response->assertJson(fn(AssertableJson $json) => $json->has('projects',1)->has(
            'projects.0',fn(AssertableJson $json) => $json->where('name','Portfolio')->etc()
        ));
        $response = $this->getJson('api/projects-by-technology/5');
        $response->assertJson(fn(AssertableJson $json) => $json->has('projects',2)->has(
            'projects.0',fn(AssertableJson $json) => $json->where('name','Portfolio')->etc()
        )->has(
            'projects.1',fn(AssertableJson $json) => $json->where('name','Portfolio1')->etc()
        ));

        $response = $this->getJson('api/projects-by-technology/7');
        $response->assertJson(fn(AssertableJson $json) => $json->has('projects',1)->has(
            'projects.0',fn(AssertableJson $json) => $json->where('name','Portfolio1')->etc()
        ));

        $response = $this->postJson("api/technology",['json'=>json_encode(
            [
                'name'=> ''
        ])]);

        $response->assertStatus(400);
        $response = $this->getJson('api/projects-by-technology/11');
        $response->assertStatus(404);
        $response = $this->getJson('api/technologies-by-project/11');
        $response->assertStatus(404);
        $response = $this->getJson('api/add-technology/11/7');
        $response->assertStatus(404);
        $response = $this->getJson('api/add-technology/2/11');
        $response->assertStatus(404);
        $response = $this->getJson('api/add-technology/1/1');
        $response->assertStatus(403);
        $response = $this->getJson('api/remove-technology/1/3');
        $response->assertStatus(404);
        $response = $this->deleteJson('api/technology/4');
        $response->assertStatus(404);
    }

    public function test_technology_like(){
        $jwt_token = $this->GetToken();
        $technologies = ['Angular','Angular-material','Laragon','Laravel','Git','GitLab', 'Docker', 'React','React native', 'Django', 'Flask', 'Symfony'];
        $this->withHeader('Authorization','Bearer '.$jwt_token);
        foreach ($technologies as $technology) {
            $this->postJson("api/technology",['json'=>json_encode(
                [
                    'name'=> $technology
            ])]);
        }
        $response = $this->getJson('api/technologies-like/angular');
        $response->assertJson(fn(AssertableJson $json)=> $json->has('technologies',2));
        $response = $this->getJson('api/technologies-like/lara');
        $response->assertJson(fn(AssertableJson $json)=> $json->has('technologies',2)->has(
            'technologies.0',fn(AssertableJson $json) => $json->where('name', 'Laragon')->etc()
        )->has(
            'technologies.1',fn(AssertableJson $json) => $json->where('name', 'Laravel')->etc()
        ));
        $response = $this->getJson('api/technologies-like/artisan');
        $response->assertJson(fn(AssertableJson $json)=> $json->has('technologies',0));
        $response = $this->getJson('api/technologies-like/docker');
        $response->assertJson(fn(AssertableJson $json)=> $json->has('technologies',1));
    }

}

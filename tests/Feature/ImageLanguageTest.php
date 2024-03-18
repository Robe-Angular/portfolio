<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class ImageLanguageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_create_edit_image_language(): void
    {
        $jwt_token = $this->GetToken();
        Storage::fake('local');
        
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $file = UploadedFile::fake()->image('number0.jpg');
        $this->putJson('/api/file/1', [
            'file0' => $file,
        ]);
        $response = $this->getJson('api/list-by-image/1');
        $response->assertJson(fn(AssertableJson $json) => $json->has('images_language',3));
        $response = $this->getJson('api/list-by-image/2');
        $response->assertJson(fn(AssertableJson $json) => $json->has('images_language',0));
        $this->putJson('api/image-language/1');
        $response = $this->putJson('api/image-language/1',['json'=>
            json_encode([
                'description_language'=> ''
        ])]);
        $response->assertStatus(400);
        $response = $this->putJson('api/image-language/4',['json'=>
            json_encode([
                'description_language'=> 'Content1'
        ])]);
        $response->assertStatus(404);
        for( $i = 1; $i < 4; $i++ ) {
            $this->putJson('api/image-language/'.$i,['json'=>
                json_encode([
                    'description_language'=> 'Content'.$i
            ])]); 
        }
        $response = $this->getJson('api/list-by-image/1');
        $response->assertJson(fn(AssertableJson $json) => $json->has('images_language',3)->has(
            'images_language.0',fn(AssertableJson $json) => $json->where('description_language',"Content1")->etc()
        )->has(
            'images_language.1',fn(AssertableJson $json) => $json->where('description_language',"Content2")->etc()
        )->has(
            'images_language.2',fn(AssertableJson $json) => $json->where('description_language',"Content3")->etc()
        ));
        $response = $this->getJson('api/by-description/Content6');
        $response->assertStatus(400);
        for( $i = 1; $i < 4; $i++ ){
            $response = $this->getJson('api/by-description/Content'. $i);
            $response->assertStatus(200);
        }
    }
}

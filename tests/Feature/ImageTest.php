<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\Fluent\AssertableJson;


class ImageTest extends TestCase
{
    use RefreshDatabase;
    use MakesHttpRequests;
    public function test_upload_delete_on_deleting_project_event(): void
    {
        
        $jwt_token = $this->GetToken();
        Storage::fake('local');
        
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $file = UploadedFile::fake()->image('number0.jpg');
        $this->flushHeaders();
        $this->putJson('/api/file/1', [
            'file0' => $file,
        ]);
        Storage::assertMissing($file->hashName());
        //
        $file = UploadedFile::fake()->image('number.jpg');
        $this->withHeader('Authorization','Bearer '.$jwt_token)->putJson('/api/file/1', [
            'file0' => $file,
        ]);
        $file_name1 = $file->hashName();
        Storage::assertExists($file_name1);
        //
        $file = UploadedFile::fake()->image('number.jpg');
        $this->withHeader('Authorization','Bearer '.$jwt_token)->putJson('/api/file/1', [
            'file0' => $file,
        ]);
        $file_name2 = $file->hashName();

        Storage::assertExists($file_name2);
        //
        $file = UploadedFile::fake()->image('number3.jpg');
        $this->putJson('/api/file/2', [
            'file0' => $file,
        ]);
        Storage::assertMissing($file->hashName());

        //
        $file = UploadedFile::fake()->image('number2.jpg');
        
        $this->flushHeaders();
        $response = $this->putJson('/api/file/1', [
            'file0' => $file,
        ]);
        
        Storage::assertMissing($file->hashName());

        $file = UploadedFile::fake()->image('number5.jpg');
        $this->withHeader('Authorization','Bearer '."Askdfg".$jwt_token)->put('/api/file/1', [
            'file0' => $file,
        ]);
        Storage::assertMissing($file->hashName());
        $this->assertDatabaseHas('images',[
            'image_name'=>$file_name1,
            'project_id' => 1
        ]);
        $this->assertDatabaseHas('images',[
            'image_name'=>$file_name2,
            'project_id' => 1
        ]);
        $this->assertDatabaseCount('images',2);
        $this->withHeader('Authorization','Bearer '.$jwt_token)
        ->deleteJson('api/project/1');
        Storage::assertMissing($file_name1);
        Storage::assertMissing($file_name2);
        $this->assertDatabaseCount('images',0);
    }

    public function test_get_by_id_destroy(): void
    {
        
        $jwt_token = $this->GetToken();
        Storage::fake('local');
        
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        for( $i = 0; $i < 5; $i++ ) {
            $file = UploadedFile::fake()->image('number0'.$i.'.jpg');
            $this->putJson('/api/file/2', [
                'file0' => $file,
            ]);
            Storage::assertExists($file->hashName());
        }
        $file = UploadedFile::fake()->image('number5.jpg');
        $file_name = $file->hashName();
        $this->putJson('/api/file/2', [
            'file0' => $file
        ]);
        $response = $this->getJson('api/images/2');
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has('images',6)->has('images.5',fn(AssertableJson $json) =>
                $json->where('image_name',$file_name)->etc()
            )
        );
        Storage::assertExists($file_name);
        $this->TryPostTestProject($jwt_token,'portfolio2',true);
        for( $i = 5; $i < 10; $i++ ) {
            $file = UploadedFile::fake()->image('number0'.$i.'.jpg');
            $this->putJson('/api/file/3', [
                'file0' => $file,
            ]);
        }
        $this->deleteJson('api/image/8');
        Storage::assertMissing($file_name);
        $response = $this->getJson('api/images/2');
        $response->assertJson(fn(AssertableJson $json) =>
            $json->has('images',5)
        );
    }

    public function test_set_main(){
        $jwt_token = $this->GetToken();
        Storage::fake('local');
        $this->TryPostTestProject($jwt_token,'portfolio',true);
        $file = UploadedFile::fake()->image('number5.jpg');
        $file_name = $file->hashName();
        $this->putJson('/api/file/4', [
            'file0' => $file
        ]);
        $this->getJson('api/set-main/14');
        $this->getJson('api/project/4');
        $response = $this->deleteJson('api/image/14');
        $response->assertJsonPath('code',409);
        $this->getJson('api/project/4');
        Storage::assertExists($file_name);
    }


}

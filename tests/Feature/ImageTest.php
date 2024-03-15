<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;


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
        $this->put('/api/file/1', [
            'file0' => $file,
        ]);
        Storage::assertMissing($file->hashName());
        //
        $file = UploadedFile::fake()->image('number.jpg');
        $this->withHeader('Authorization','Bearer '.$jwt_token)->put('/api/file/1', [
            'file0' => $file,
        ]);
        $file_name1 = $file->hashName();
        Storage::assertExists($file_name1);
        //
        $file = UploadedFile::fake()->image('number.jpg');
        $this->withHeader('Authorization','Bearer '.$jwt_token)->put('/api/file/1', [
            'file0' => $file,
        ]);
        $file_name2 = $file->hashName();

        Storage::assertExists($file_name2);
        //
        $file = UploadedFile::fake()->image('number3.jpg');
        $this->put('/api/file/2', [
            'file0' => $file,
        ]);
        Storage::assertMissing($file->hashName());

        //
        $file = UploadedFile::fake()->image('number2.jpg');
        
        $this->flushHeaders();
        $response = $this->put('/api/file/1', [
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
}

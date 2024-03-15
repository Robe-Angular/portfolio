<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectLanguageController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/make-admin',[AdminController::class,'index']);

Route::controller(AuthController::class)->group(function(){
    Route::post('login','login')->name('login');
    //Route::post('register','register');
    Route::post('logout','logout');
    Route::post('refresh','refresh');

});

Route::controller(AdminController::class)->group(function(){
    Route::get('admin/info','info');
    Route::get('make-admin','index');
});

Route::controller(ProjectController::class)->group(function(){
    Route::post('project','store');
    Route::put('project/{project_id}','edit');
    Route::get('project/{project_id}','detail');
    Route::get('project','index');
    Route::delete('project/{project_id}','delete');
});

Route::controller(ProjectLanguageController::class)->group(function(){
    Route::get('project-language/{project_language_id}','detail');
    Route::put('project-language/{project_language_id}','update');
});

Route::controller(ImageController::class)->group(function(){
    Route::put('file/{project_id}','upload');
});
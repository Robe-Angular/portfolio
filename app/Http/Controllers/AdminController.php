<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('assign.guard:admin',['except'=>[
            'index'
        ]]);
    }
    
    public function index(){
        
       $admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@admin.admin',
            'password' => \Illuminate\Support\Facades\Hash::make('LaravelJWT')//'LaravelJWT' Is the password

        ]);
        
        return response()->json(['message' => 'success'], 200);
    }

    public function info(){
        $info = auth()->user();
        return response()->json([
            'info' => $info
        ],200);
    }

}

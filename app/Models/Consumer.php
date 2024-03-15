<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Consumer extends Authenticatable implements JWTSubject
{
    use HasFactory;
    
    protected $table = 'consumers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password'
    ];
    protected $hidden = [
        'password'
    ];
    public function getJWTIdentifier(){
        return $this->getKey();
    }
    public function getJWTCustomClaims(){
        return [];
    }

}

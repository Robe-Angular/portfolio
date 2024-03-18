<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;
    protected $table = 'technologies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];
    
    public function projects(){
        return $this->belongsToMany(Project::class,'project_technology','technology_id','project_id');
    }
    
}

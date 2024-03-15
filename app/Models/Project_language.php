<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_language extends Model
{
    use HasFactory;
    protected $table = 'project_language';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title_language',
        'content_language'
    ];
    
    public function project(){
        return $this->belongsTo(Project::class);
    }
}

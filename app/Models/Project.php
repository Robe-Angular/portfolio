<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'url',
        'confidencial'
    ];

    public function technologies(){
        return $this->belongsToMany(Technology::class,'project_technology','project_id','technology_id');
    }

    public function images(){
        return $this->hasMany(Image::class,'project_id','id');
    }

    protected static function booted():void{
        static::deleting(function($model){
            $image_names = $model->images->pluck('image_name')->toArray();
            Storage::disk('local')->delete($image_names);
        });
    }
}

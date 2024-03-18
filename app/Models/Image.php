<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'image_name',
        'description'
    ];
    
    public function imagesLanguage(){
        return $this->hasMany(Image_language::class);
    }
    public function project(){
        return $this->belongsTo(Project::class);
    }

    protected static function booted():void{
        static::deleting(function($model){
            $image_name = $model->image_name;
            Storage::disk('local')->delete($image_name);
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image_language extends Model
{
    use HasFactory;
    protected $table = 'image_language';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description_language',
    ];
    
    public function image(){
        return $this->belongsTo(Image::class);
    }
}

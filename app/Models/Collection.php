<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','collection_type_id','collector_id'];

    protected $appends = ['download_url','thumbnail_url'];
    
    public function getDownloadUrlAttribute()
    {
        return URL::to('collection/download/'.$this->id);
    }

    public function getThumbnailUrlAttribute()
    {
        $files = File::allFiles(public_path('/thumbnail/'.$this->id));
        return url(('/thumbnail/'.$this->id.'/'.basename($files[0])));
    }
}

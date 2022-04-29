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

    protected $fillable = ['name','description','condition_type','collection_type_id','collector_id'];

    protected $appends = ['download_url','thumbnail_url'];
    
    public function getDownloadUrlAttribute()
    {
        // return URL::to('api/collection/download/'.$this->id);
         $files = Storage::disk('files')->files($this->id);
        // dd(sizeof($files));
        if(sizeof($files)>0)
            return url(('/collection_files/'.$this->id.'/'.explode("/",$files[0])[1]));
        else
            return url(('/thumbnail/icon-no-image.png'));
    }

    public function getThumbnailUrlAttribute()
    {
        $files = Storage::disk('collection')->files($this->id);
        // dd(sizeof($files));
        if(sizeof($files)>0)
            return url(('/thumbnail/'.$this->id.'/'.explode("/",$files[0])[1]));
        else
            return url(('/thumbnail/icon-no-image.png'));
    }
    public function collector(){
        return $this->belongsTo(Collector::class);
    }
}


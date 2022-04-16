<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class Collector extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','username','password','api_token'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($collector) {
            $collector->attributes['api_token'] = self::setApiTokenAttribute();
        });
    }
    protected static function setApiTokenAttribute(){
        do{
            $api_token = Str::random(10);
            $hashed_token = Crypt::encryptString($api_token);
        }
        while(self::where('api_token',$hashed_token)->exists());
        return $hashed_token;
    }

    public function getApiTokenAttribute()
    {
        return Crypt::decryptString($this->attributes['api_token']);
    }
}

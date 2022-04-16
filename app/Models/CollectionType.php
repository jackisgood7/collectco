<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionType extends Model
{
    use HasFactory;

    protected $fillablle = ['name','require_thumbnail'];

    protected $hidden = ['created_at','updated_at'];
}

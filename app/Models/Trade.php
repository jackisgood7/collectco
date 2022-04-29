<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;
    const pending = 'pending' ;
    const approved = 'approved';
    const rejected = 'rejected';
    protected $fillable = ['status','request_collection_id','target_collection_id','requestor_user_id','target_user_id'];
}

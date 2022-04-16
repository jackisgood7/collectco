<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollectionType;

class CollectionTypeController extends Controller
{
    public function index(){
        return response()->json([
            'data' => CollectionType::all()
        ]);
    }
}

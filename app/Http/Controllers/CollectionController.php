<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function show($id){
        return 'get collection details';
    }

    public function store(Request $request){
        return 'adding collection';
    }

    public function getUserCollection($id){
        return 'get all collection';
    }
}

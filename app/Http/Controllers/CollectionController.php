<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    public function show($id){
        $collection = Collection::findOrFail($id);
        return response()->json([
            'data' => $collection
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->input(),[
            'name' => ['required'],
            'description' => ['required'],
            'collection_type_id' => ['required','exists:App\Models\CollectionType,id'],
            'collector_id' => ['required','exists:App\Models\Collector,id'],
            'file' => ['required']
        ]);
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $collection_type_id = $data['collection_type_id'];
            if(CollectionType::find($collection_type_id)->require_thumbnail && !isset($data['thumbnail'])){
                $validator->errors()->add('thumbnail','Thumbnail is required');
            }
        });
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        $input = (object)$request->input();
        $collection = Collection::create([
            'name' => $input->name,
            'description' => $input->description,
            'collection_type_id' => $input->collection_type_id,
            'collector_id' => $input->user_id
        ]);
        return response()->json([
            'data' => $collection
        ]);
    }

}

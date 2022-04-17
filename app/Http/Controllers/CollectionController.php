<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use File;
use DB;

class CollectionController extends Controller
{
    private function getUploadPath($type,$id,$file){
        return 'public/'.$type.'/'.$id;
    }

    private function makeDirectory($id){
        $path = public_path('/thumbnail/'.$id);
        File::makeDirectory($path,0777,true,true);
    }

    private function storeThumbnail($file,$collection_id,$file_name){
        $file->move(public_path('/thumbnail/'.$collection_id),$file_name);
    }
    public function show($id){
        $collection = Collection::findOrFail($id);
        return response()->json([
            'data' => $collection
        ]);
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(),[
                'name' => ['required'],
                'description' => ['required'],
                'collection_type_id' => ['required','exists:App\Models\CollectionType,id'],
                'collector_id' => ['required','exists:App\Models\Collector,id'],
                'file' => ['required','mimes:jpg']
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
                'collector_id' => $input->collector_id
            ]);
            Storage::put($this->getUploadPath('collection',$collection->id,$request->file),$request->file);
            $require_thumbnail = CollectionType::findOrFail($input->collection_type_id)->require_thumbnail;
            $this->makeDirectory($collection->id);
            if($require_thumbnail)
                $this->storeThumbnail($request->thumbnail,$collection->id,$request->thumbnail->getClientOriginalName());
            else
                $this->storeThumbnail($request->file,$collection->id,$request->file->getClientOriginalName());
            $input = (object)$request->input();
            //return Storage::download('public/image.png');
            DB::commit();
            return response()->json([
                'data' => $collection
            ]);
        }catch(\Exception $e){
            DB::rollback();
            dd($e);
        }
    }

    public function download($id){
        return Storage::download('public/image.png');
    }
}

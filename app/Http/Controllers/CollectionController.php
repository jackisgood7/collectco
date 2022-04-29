<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Trade;
use App\Models\CollectionType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use File;
use DB;

class CollectionController extends Controller
{
    
    private function ApiResponse($responseCode,$responseMessage,$data = null){
        if($data == null){
            return response()->json([
                'responseCode' => $responseCode,
                'responseMessage' => $responseMessage,
            ]);
        }
        else{
            return response()->json([
                'responseCode' => $responseCode,
                'responseMessage' => $responseMessage,
                'data' => $data
            ]);
        }
    }
    private function getUploadPath($type,$id,$file){
        return 'public/'.$type.'/'.$id;
    }

    private function makeDirectory($id,$directory_type){
        Storage::disk($directory_type)->makeDirectory($id,0777,true,true);
    }

    private function storeThumbnail($file,$collection_id,$file_name,$directory_type){
        Storage::disk($directory_type)->put($collection_id.'/'.$file_name,file_get_contents($file));
        //$file->move(public_path('/thumbnail/'.$collection_id),$file_name);
    }
    public function show($id){
        try{
            $collection = Collection::findOrFail($id);
            return $this->ApiResponse(0,'Found Collection',$collection);
        }
        catch(\Exception $e){
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(),[
                'name' => ['required'],
                'description' => ['required'],
                'condition_type' => ['required','in:new,used'],
                'collection_type_id' => ['required','exists:App\Models\CollectionType,id'],
                'collector_id' => ['required','exists:App\Models\Collector,id'],
                'file' => ['required'],
                'thumbnail' => ['nullable','mimes:jpg,jpeg,png']

            ]);
            $validator->after(function ($validator) {
                $data = $validator->getData();
                $collection_type_id = $data['collection_type_id'];
                if(CollectionType::findOrFail($collection_type_id)->require_thumbnail && !isset($data['thumbnail'])){
                    $validator->errors()->add('thumbnail','Thumbnail is required');
                }
                $config_upload_file = config('constants.collection_types');
                if(isset($config_upload_file[(string)$collection_type_id])){
                    $config = $config_upload_file[(string)$collection_type_id];
                    if($config['mime'][0] != '*'){
                        $valid = false;
                        $valid_type = '';
                        foreach($config['mime'] as $c){
                            if($c==$data['file']->getMimeType()) $valid = true;
                            $valid_type .= $c.',';
                        }
                        if($valid==false)
                        $validator->errors()->add('file','Supported file format are '.$valid_type);

                        // if(array_search($data['file']->getMimeType(),$config['mime'])==false)
                        //     $validator->errors()->add('file',$data['file']->getMimeType());
                    }
                }
                else
                    throw new Exception('Config for this collection type not found');
            });
            if($validator->fails()){
                return $this->ApiResponse(1,json_encode($validator->errors()));
            }
            $input = (object)$request->input();
            $collection = Collection::create([
                'name' => $input->name,
                'description' => $input->description,
                'condition_type' => $input->condition_type,
                'collection_type_id' => $input->collection_type_id,
                'collector_id' => $input->collector_id
            ]);
            Storage::put($this->getUploadPath('collection',$collection->id,$request->file),$request->file);
            $require_thumbnail = CollectionType::findOrFail($input->collection_type_id)->require_thumbnail;
            $this->makeDirectory($collection->id,'collection');
            $this->makeDirectory($collection->id,'files');

            // if($require_thumbnail){
                $this->storeThumbnail($request->thumbnail,$collection->id,$request->thumbnail->getClientOriginalName(),'collection');
                $this->storeThumbnail($request->file,$collection->id,$request->file->getClientOriginalName(),'files');
            // }
            // else{
            //     $this->storeThumbnail($request->file,$collection->id,$request->file->getClientOriginalName(),'collection');
            //     $this->storeThumbnail($request->file,$collection->id,$request->file->getClientOriginalName(),'files');
            // }
            $input = (object)$request->input();
            //return Storage::download('public/image.png');
            DB::commit();
            return $this->ApiResponse(0,'Added Collection',$collection);
        }catch(\Exception $e){
            DB::rollback();
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function delete(Request $request){
        try{
            $id = $request->collection_id;
            $delete_result = Collection::where('id',$id)->delete();
            Trade::where('target_collection_id',$id)->orWhere('request_collection_id',$id)->delete();
            if($delete_result)
                return $this->ApiResponse(0,'Deleted Collection');
        }catch(\Exception $e){
            DB::rollback();
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function download($id){
        $files = Storage::files('public/collection/'.$id);
        return Storage::download('public/collection/'.$id.'/'.explode("/",$files[0])[3]);
    }

}

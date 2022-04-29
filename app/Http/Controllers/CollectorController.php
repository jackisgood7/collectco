<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collector;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Collection;

class CollectorController extends Controller
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
    public function login(Request $request){
        $user = Collector::where('username',$request->username)->orWhere('email',$request->username)->first();
        if($user!=null){
            if(Hash::check($request->password,$user->password)){
                $user = Collector::hidePassword($user);
                return $this->ApiResponse(0,'Login successfully',$user);
            } 
            return $this->ApiResponse(1,config('constants.message.wrong_credentials'));
        }else{
            return $this->ApiResponse(1,config('constants.message.wrong_credentials'));
        }
    }
    
    public function getUsers(Request $request){
        if($request->has('user_id'))
            $user_list = Collector::where('id','!=',$request->query('user_id'))->get();
        else
            $user_list = Collector::all();
        foreach($user_list as $user){
            $user = Collector::hidePassword($user);
        }
        return $this->ApiResponse(0,'Get all users list',$user_list);
    }

    public function getUser($id){
        $user = Collector::findorFail($id);
        if($user)
            return $this->ApiResponse(0,'Found User',Collector::hidePassword($user));
        else
        return $this->ApiResponse(1,config('constants.message.invalid_format'));
    }

    public function register(Request $request){
        $validator = Validator::make($request->input(), [
            'username' => ['required','unique:App\Models\Collector,username'],
            'password' => ['required', Password::min(8)->numbers()->symbols()->mixedCase()],
            'name' => ['required'],
            'email' => ['required','email','unique:App\Models\Collector,email'],
        ]);
        if($validator->fails()){
            return $this->ApiResponse(1,$validator->errors());
        }
        $input = (object)$request->input();
        $user = Collector::create([
            'username' => $input->username,
            'email' => $input->email,
            'password' => Hash::make($input->password),
            'name' => $input->name
        ]);
        return $this->ApiResponse(0,'Registered User Successfully',Collector::hidePassword($user));
    }

    public function getCollection(Request $request,$id){
        try{
            if($request->has('type')){
                $collections = Collector::join('collections','collectors.id','collections.collector_id')->
                where('collections.collector_id',$id)->where('collections.collection_type_id',$request->query('type'))->select('collections.*')->get();
                foreach($collections as $c){
                    $collection = Collection::find($c->id);
                    $c->thumbnail_url = $collection->thumbnail_url;
                    $c->download_url = $collection->download_url;
                }
                return $this->ApiResponse(0,'Get user collection',$collections);
            }else{
                $user = Collector::findOrFail($id);
                $collections = $user->collections;
            }
            return $this->ApiResponse(0,'Get Collection List',$collections);
        }catch(\Exception $e){
            return $this->ApiResponse(1,$e->getMessage());
        }
    }
}

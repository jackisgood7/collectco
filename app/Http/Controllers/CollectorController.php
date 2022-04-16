<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Collector;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class CollectorController extends Controller
{
    public function login(Request $request){
        $user = Collector::where('username',$request->username)->orWhere('email',$request->username)->first();
        if($user!=null){
            if(Hash::check($request->password,$user->password)){
                $user = Collector::hidePassword($user);
                return response()->json([
                    'data' => $user
                ]);
            } 
            return response()->json([
                'error' => config('constants.message.wrong_credentials')
            ]); 
        }else{
            return response()->json([
                'error' => config('constants.message.wrong_credentials')
            ]);
        }
    }
    
    public function getUsers(){
        $user_list = Collector::all();
        foreach($user_list as $user){
            $user = Collector::hidePassword($user);
        }
        return response()->json([
            'data' => $user_list
        ]);
    }

    public function getUser($id){
        $user = Collector::findorFail($id);
        if($user)
            return response()->json([
                'data' => Collector::hidePassword($user)
            ]);
        else
        return response()->json([
            'error' => config('constants.message.invalid_format')
        ]); 
    }
    public function register(Request $request){
        $validator = Validator::make($request->input(), [
            'username' => ['required','unique:App\Models\Collector,username'],
            'password' => ['required', Password::min(8)->numbers()->symbols()->mixedCase()],
            'name' => ['required'],
            'email' => ['required','email','unique:App\Models\Collector,email'],
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        $input = (object)$request->input();
        $user = Collector::create([
            'username' => $input->username,
            'email' => $input->email,
            'password' => Hash::make($input->password),
            'name' => $input->name
        ]);
        return response()->json([
            'data' => $user
        ]);

    }
}

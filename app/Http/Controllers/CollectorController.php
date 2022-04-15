<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectorController extends Controller
{
    public function login(Request $request){
        return 'login function';
    }
    public function getUsers(){
        return 'user list';
    }

    public function getUser($id){
        return 'user '.$id;
    }
    public function register(Request $request){
        return 'user list';
    }
}

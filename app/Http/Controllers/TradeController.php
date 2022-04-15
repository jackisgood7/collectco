<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function getUserTrade($id){
        return 'get all trade records for user '.$id;
    }

    public function show($id){
        return 'get trade details'; 
    }

    public function store(Request $request){
        return 'adding trade request';
    }

    public function update(Request $request){
        return 'approve/rejecting trade request';
    }
}

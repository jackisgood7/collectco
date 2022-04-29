<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionTypeController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\ChatMessageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('broadcast',[ChatMessageController::class,'broadcast']);

Route::get('/', function () {
    return view('welcome');
});

//all api routes are here 
Route::group(['prefix'=>'api','middleware' => 'api.auth'],function() {
    
    Route::group(['prefix'=>'chat'],function(){
        Route::get('getMessage',[ChatMessageController::class,'getMessage']);
        Route::post('send',[ChatMessageController::class,'send']);
    });
    
    Route::group(['prefix'=>'users'],function() {
        //login user
        Route::post('login',[CollectorController::class,'login']);
        //register user
        Route::post('register',[CollectorController::class,'register']);
        //get all users
        Route::get('',[CollectorController::class,'getUsers']);
        //get specific
        Route::get('{id}',[CollectorController::class,'getUser']);
        //get collection list for a user
        Route::get('collection/{id}',[CollectorController::class,'getCollection']);
    });
    Route::group(['prefix'=>'collection'],function() {
        //add collection
        Route::post('store',[CollectionController::class,'store']);
        //get collection details
        Route::get('{id}',[CollectionController::class,'show']);
        //user download collection
        Route::get('download/{id}',[CollectionController::class,'download']);
        //
        Route::post('delete',[CollectionController::class,'delete']);
    });
    //get all collection types 
    Route::get('collectionTypes',[CollectionTypeController::class,'index']);
    Route::group(['prefix'=>'trade'],function() {
        //get all trade list of a user
        Route::get('user/{id}',[TradeController::class,'getUserTrade']);
        //get trade details
        Route::get('{id}',[TradeController::class,'show']);
        //create trade request with other user
        Route::post('store',[TradeController::class,'store']);
        //approve or reject the 
        Route::post('update',[TradeController::class,'update']);
    });
});
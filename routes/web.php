<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionTypeController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\TradeController;

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

Route::get('/', function () {
    return view('welcome');
});

//all api routes are here 
Route::group(['prefix'=>'api'],function() {
    Route::group(['prefix'=>'users'],function() {
        //login user
        Route::post('login',[CollectorController::class,'login']);
        //register user
        Route::post('register',[CollectionController::class,'register']);
        //get all users
        Route::get('',[CollectorController::class,'getUsers']);
        //get specific
        Route::get('{id}',[CollectorController::class,'getUser']);
    });
    Route::group(['prefix'=>'collection'],function() {
        //get collection list for a user
        Route::get('getUserCollection/{id}',[CollectionController::class,'getUserCollection']);
        //add collection
        Route::post('store',[CollectionController::class,'store']);
        //get collection details
        Route::get('{id}',[CollectionController::class,'show']);
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
        Route::put('update',[TradeController::class,'update']);
    });
    
});
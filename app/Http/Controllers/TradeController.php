<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Trade;
use App\Models\Collector;
use App\Models\Collection;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
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
    // public function getUserTrade(Request $request,$id){
    //     try{
    //         $user = Collector::findOrFail($id);
    //         if($request->has('target_id')){
    //             $trades = Trade::where('target_user_id',$id)->get();
    //         }
    //         else
    //             $trades = Trade::where('requestor_user_id',$id)->orWhere('target_user_id',$id)->get();
    //         return $this->ApiResponse(0,'Get user trade history',$trades);
    //     }catch(\Exception $e){
    //         return $this->ApiResponse(1,$e);
    //     }
    // }
    
    public function getUserTrade(Request $request,$id){
        try{
            $user = Collector::findOrFail($id);
            if($request->has('status')&&$request->query('status')==Trade::pending){
                $trades = Trade::where('target_user_id',$id)->where('status',Trade::pending)->get();
                //$trades = Trade::where('status',Trade::pending)->where('target_user_id',$id)->orWhere('requestor_user_id',$id)->get();
            }
            else
                $trades = Trade::where('status','!=',Trade::pending)->where('requestor_user_id',$id)->orWhere('target_user_id',$id)->get();
            foreach($trades as $trade){
                $trade->target_collection = Collection::find($trade->target_collection_id);
                $trade->requestor_collection = Collection::find($trade->request_collection_id);
                $trade->request_user = Collector::find($trade->requestor_user_id)->only(['name','id']);
                $trade->target_user = Collector::find($trade->target_user_id)->only(['name','id']);
            }
            return $this->ApiResponse(0,'Get user trade history',$trades);
        }catch(\Exception $e){
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function show($id){
        try{
            $trade = Trade::findOrFail($id);
            return $this->ApiResponse(0,'Get trade details',$trade);
        }catch(\Exception $e){
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->input(),[
                'request_collection_id' => ['required','exists:App\Models\Collection,id'],
                'target_collection_id' => ['required','exists:App\Models\Collection,id'],
            ]);
            // $validator->after(function ($validator){
            //     $data = $validator->getData();
            //     if(Trade::where('request_collection_id',$data['request_collection_id'])->where('status',Trade::pending)->count()>0)
            //         $validator->errors()->add('request_collection','You have');
            // });
            if($validator->fails()){
                return $this->ApiResponse(1,$validator->errors());
            }
            $trade = Trade::create([
                'status' => Trade::pending,
                'request_collection_id' => $request->request_collection_id,
                'target_collection_id' => $request->target_collection_id,
                'requestor_user_id' => Collection::findOrFail($request->request_collection_id)->collector_id,
                'target_user_id' => Collection::findOrFail($request->target_collection_id)->collector_id
            ]);
            DB::commit();
            return $this->ApiResponse(0,'Request Trade Successfully',$trade);
        }catch(\Exception $e){
            DB::rollback();
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function update(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->input(),[
                'status' => ['required','in:approved,rejected'],
                'trade_id' => ['required','exists:App\Models\Trade,id']
            ]);
            $trade = Trade::findOrFail($request->trade_id);
            if($validator->fails()){
                return $this->ApiResponse(1,$validator->errors());
            }
            $trade_collection = Collection::find($trade->request_collection_id);
            $target_collection = Collection::find($trade->target_collection_id);
            if($target_collection==null || $trade_collection == null){
                Trade::find($trade->id)->delete();
                return $this->ApiResponse(1,config('constants.message.collection_unavailable'));
            }
            if($trade_collection->collector->id!=$trade->requestor_user_id || $target_collection->collector->id!=$trade->target_user_id){
                Trade::find($trade->id)->delete();
                return $this->ApiResponse(1,config('constants.message.collection_unavailable'));
            }
            if($trade->status!=Trade::pending)
                return $this->ApiResponse(1,config('constants.message.invalid_format'));
            $trade->status = $request->status;
            $trade->save();
            Collection::where('id',$trade->request_collection_id)->update([
                'collector_id' => $trade->target_user_id
            ]);
            Collection::where('id',$trade->target_collection_id)->update([
                'collector_id' => $trade->requestor_user_id
            ]);
            DB::commit();
            return $this->ApiResponse(0,'Successfully '.$request->status.' trade');
        }catch(\Exception $e){
            DB::rollback();
            dd($e);
            return $this->ApiResponse(1,$e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Events\SendMessage;
use DB;

class ChatMessageController extends Controller
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
    public function send(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->input(),[
                'message' => 'required',
                'from_id' => ['required','exists:App\Models\Collector,id'],
                'to_id' => ['required','exists:App\Models\Collector,id'],
                'channel_id' => ['required','exists:App\Models\ChatChannel,id']
            ]);
            if($validator->fails()) return $this->ApiResponse(1,$validator->errors());
            $input = (object)$request->input();
            $message = ChatMessage::create([
                'channel_id' => $input->channel_id,
                'from_id' => $input->from_id,
                'to_id' => $input->to_id,
                'message' => $input->message
            ]);
            if($message){
                DB::commit();
                broadcast(new SendMessage($message, $input->channel_id))->toOthers();
                return $this->ApiResponse(0,'Message sent',$message); 
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->ApiResponse(1,$e->getMessage());
        }
    }

    public function broadcast(){
        $message = array(
            'channel_id' => 'channel_1',
            'from_id' => 1,
            'to_id' => 2,
            'message' => 'Dont play play leh'
        );
        broadcast(new SendMessage($message,'channel_1'))->toOthers();
    }
    
    public function getMessage(Request $request){
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->input(),[
                'user_id' => ['required','exists:App\Models\Collector,id'],
                'target_user_id' => ['required','exists:App\Models\Collector,id']
            ]);
            if($validator->fails()){
                return $this->ApiResponse(1,$validator->errors());
            }
            // return ChatChannel::get();
            $channel = ChatChannel::where('collector_id_1',$request->user_id)->where('collector_id_2',$request->target_user_id)->first();
            $channel_2 = ChatChannel::where('collector_id_2',$request->user_id)->where('collector_id_1',$request->target_user_id)->first();
            if(!$channel && !$channel_2){
                $channel = ChatChannel::create([
                    'collector_id_1' => $request->user_id,
                    'collector_id_2' => $request->target_user_id
                ]);
            }
            if(!$channel) $channel = $channel_2;
            $messages = ChatMessage::where('channel_id',$channel->id)->get();
            $data = array(
                'channel_id' => $channel->id,
                'messages' => $messages
            );
            DB::commit();
            return $this->ApiResponse(0,'Get message list',$data);
        }catch(\Exception $e){
            DB::rollback();
            return $this->ApiResponse(1,$e->getMessage());
        }
        
    }
}

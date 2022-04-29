<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatChannel;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('channel_{id}',function ($user,$channel_id){
//     $channel = ChatChannel::find($channel_id);
//     if($user->id == $channel->collector_id_1 || $user->id == $channel->collector_id_2 )
//         return true;
//     else
//         return false;
// });
// Broadcast::channel('channel_{id}',function (){
//     return true;
// });

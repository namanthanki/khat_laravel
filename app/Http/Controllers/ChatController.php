<?php

namespace App\Http\Controllers;

use App\Models\Messages;
use App\Models\User;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    static function index(Request $request)
    {
        $user = User::where('uid', $request->uid)->first();
    
        $incomingId = $request->uid;
        $outgoingId = auth() -> user() -> uid;
        
        if($request -> ajax()) {

        $messages = Messages::select('messages.*', 'users.*')
            ->leftJoin('users', 'users.uid', '=', 'messages.outgoing_message_id')
            ->where(function ($query) use ($incomingId, $outgoingId) {
                $query->where('incoming_message_id', $incomingId)
                    ->where('outgoing_message_id', $outgoingId);
            })
            ->orWhere(function ($query) use ($incomingId, $outgoingId) {
                $query->where('outgoing_message_id', $incomingId)
                    ->where('incoming_message_id', $outgoingId);
            })
            ->orderBy('messages.id')
            ->get();

        $output = "";
        foreach($messages as $message) {
            if($message -> outgoing_message_id == auth() -> user() -> uid) {
                $output .= '<div class="chat outgoing">'.
                    '<div class="details">'.
                        '<p>'.$message -> message.'</p>'.
                    '</div>'.
                '</div>';
            } else {
                $output .= '<div class="chat incoming">'.
                    '<img src='.asset($user->profile_picture).'>'.
                    '<div class="details">'.
                        '<p>'.$message -> message.'</p>'.
                    '</div>'.
                '</div>';
            }
        }
        return Response($output);
        }
        return view('chat', ['user' => $user]) -> render();


        // return response()->json(['messages' => $messages]);
    }

    static function sendMessage(Request $request)
    {
        $message = new Messages();
        $message->incoming_message_id = $request -> incomingId;
        $message->outgoing_message_id = $request -> outgoingId;
        $message->message = $request -> sendMessage;
        $message->save();

        // $user = User::where('uid', $request -> outgoingId)->first();

        // $incomingId = $request -> incomingId;
        // $outgoingId = $request -> outgoingId;

        // $messages = Messages::select('messages.*', 'users.*')
        //     ->leftJoin('users', 'users.uid', '=', 'messages.outgoing_message_id')
        //     ->where(function ($query) use ($incomingId, $outgoingId) {
        //         $query->where('incoming_message_id', $incomingId)
        //             ->where('outgoing_message_id', $outgoingId);
        //     })
        //     ->orWhere(function ($query) use ($incomingId, $outgoingId) {
        //         $query->where('outgoing_message_id', $incomingId)
        //             ->where('incoming_message_id', $outgoingId);
        //     })
        //     ->orderBy('messages.id')
        //     ->get();

        // return view('chat', ['user' => $user, 'messages' => $messages]) -> render();
    }

    static function deleteMessages(Request $request)
    {

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Messages;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    static function getUsers(Request $request) {
        $uid = auth() -> user() -> uid;
        $output = "";
        if($request -> ajax()) {
            $users = User::where('uid', '!=', auth() -> user() -> uid) -> get();
            if(sizeof($users) > 0) {
                foreach($users as $user) {  
                    $incomingID = $user -> uid;
                    $latestMessage = Messages::where(function($q) use($incomingID){
                        $q->where('incoming_message_id', $incomingID)->orWhere('outgoing_message_id', $incomingID);
                    })->where(function($q) use($uid){
                        $q->where('outgoing_message_id', $uid)->orWhere('incoming_message_id', $uid);
                    })->orderBy('id', 'desc') -> first();

                    $message = "";
                    $you = "";
                    if($latestMessage) {
                        ($uid == $latestMessage -> outgoing_message_id) ? $you = "You: " : $you = "";
                        (strlen($latestMessage -> message) > 28 ? $message = substr($latestMessage -> message, 0, 28).'...' : $message = $latestMessage -> message);
                    } else {
                        $message = "No Messages Available!";
                    }
                
                    $output .= '
                    <a href='.route('chat', $user -> uid).'>'     .
                        '<div class="chat">' .
                            '<div class="profile-picture-container">' .
                                '<img src='.asset($user -> profile_picture).'>' .
                            '</div>' .
                            '<div class="chat-details">' .
                                '<h3>'.$user -> name.'</h3>' .
                                '<p>'.$you .$message.'</p>' .
                            '</div>' .
                        '</div>' .
                    '</a>';
                }
            } else {
                $output .= "No Users Available to Chat!";
            }    
            return Response($output);
        }
    }

    static function searchUser(Request $request) {
        if($request -> ajax()) {
            $uid = auth() -> user() -> uid;
            $searchContent = $request -> input('searchValue');
            
            $users = DB::table('users')
                ->where('uid', '<>', $uid)
                ->where(function ($query) use ($searchContent) {
                    $query->where('name', 'LIKE', '%'.$searchContent.'%')
                        ->orWhere('username', 'LIKE', '%'.$searchContent.'%');
                })
                ->get();

            $output = "";
            if(sizeof($users) > 0) {
                foreach($users as $user) {  
                    $incomingID = $user -> uid;
                    $latestMessage = Messages::where(function($q) use($incomingID){
                        $q->where('incoming_message_id', $incomingID)->orWhere('outgoing_message_id', $incomingID);
                    })->where(function($q) use($uid){
                        $q->where('outgoing_message_id', $uid)->orWhere('incoming_message_id', $uid);
                    })->orderBy('id', 'desc') -> first();

                    $message = "";
                    $you = "";
                    if($latestMessage) {
                        ($uid == $latestMessage -> outgoing_message_id) ? $you = "You: " : $you = "";
                        (strlen($latestMessage -> message) > 28 ? $message = substr($latestMessage -> message, 0, 28).'...' : $message = $latestMessage -> message);
                    } else {
                        $message = "No Messages Available!";
                    }
                
                    $output .= '
                    <a href='.route('chat', $user -> uid).'>' .
                        '<div class="chat">' .
                            '<div class="profile-picture-container">' .
                                '<img src='.asset($user -> profile_picture).'>' .
                            '</div>' .
                            '<div class="chat-details">' .
                                '<h3>'.$user -> name.'</h3>' .
                                '<p>'.$you .$message.'</p>' .
                            '</div>' .
                        '</div>' .
                    '</a>';
                }
            } else {
                $output .= "User Not Found!";

            }
            return Response($output);
        }
    }

    static function settings(Request $request) {
        $uid = auth() -> user() -> uid;
        $user = User::where('uid', $uid)->first();

        return view('settings', ['user' => $user]);
    }

    static function updateSettings(Request $request) {
        $uid = auth() -> user() -> uid;
        $user = User::where('uid', $uid)->first();

        if($user -> username == $request -> username && $user -> email == $request -> email) {
            return redirect() -> back();
        }

        if($user -> username == $request -> username) {
            $user -> email = $request -> email;

            $request -> validate([
                'email' => 'required|email|unique:users'
            ]);
    
            $user -> save();
    
            return redirect()->back()->with('success', 'User settings updated successfully!');
        }

        if($user -> email == $request -> email) {
            $user -> username = $request -> username;

            $request -> validate([
                'username' => 'required|unique:users',
            ]);
    
            $user -> save();
    
            return redirect()->back()->with('success', 'User settings updated successfully!');
        }

        $user -> username = $request -> username;
        $user -> email = $request -> email;
        
        $request -> validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users'
        ]);

        $user -> save();

        return redirect()->back()->with('success', 'User settings updated successfully!');
    }
}

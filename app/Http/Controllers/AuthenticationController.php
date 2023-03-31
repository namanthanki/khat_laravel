<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller {
    function login() {
        if(Auth::check()) {
            return redirect(route('/'));
        }
        return view('login');
    }

    function handleUserLogin(Request $request) {
        $request -> validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request -> only('email', 'password');
        if(Auth::attempt($credentials)) {
            $user = User::where('email', $request -> email)->first();
            $user -> status = "Active Now";
            $user -> save();
            return redirect() -> intended(route('/'));
        }

        return redirect(route('login')) -> with("error", "Invalid Login Credentials");
    }

    function register() {
        return view('register');
    }

    function uid(int $length = 64) { 
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }

    function handleUserRegistration(Request $request) {
        $request -> validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'profilePicture' => 'required',
        ]);

        // // print_r($request -> all());
        // print_r($request -> file());
        // exit;

        $userProfileImage = $request -> file('profilePicture');
        $imageName = time() . Auth::id() . "-profile." . $userProfileImage->getClientOriginalExtension();
        $uploadPath = 'images/';
        $imageURL = $uploadPath . $imageName;
        $success = $userProfileImage -> move($uploadPath, $imageName);
        
        if($success) {
            $uid = $this -> uid();
            
            $data['uid'] = $uid;
            $data['name'] = $request -> name;
            $data['username'] = $request -> username;
            $data['email'] = $request -> email;
            $data['password'] = Hash::make($request -> password);
            $data['profile_picture'] = $imageURL;
            $data['status'] = 'Active Now';

            $user = User::create($data);

            if(!$user) {
                return redirect(route('register')) -> with("error", "Something Went Wrong!");
            }

            $request -> session() -> put('uid', $uid);
            return redirect(route('login')) -> with("success", "Registration Completed Successfully!");
        }
    }

    function logout() {
        $uid = auth() -> user() -> uid;
        $user = User::where('uid', $uid)->first();
        $user -> status = "Offline Now";
        $user -> save();
        Session::flush();
        Auth::logout();
        return redirect(route('login'));
    }
}

<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthenticationController::class, 'login']) -> name('login');
Route::post('/login', [AuthenticationController::class, 'handleUserLogin']) -> name('login.post');

Route::get('/register', [AuthenticationController::class, 'register']) -> name('register');
Route::post('/register', [AuthenticationController::class, 'handleUserRegistration']) -> name('register.post');


Route::group(['middleware' => 'auth'], function() {
    Route::get('/', function() {
        return view('home');
    }) -> name('/');
    
    Route::get('/logout', [AuthenticationController::class, 'logout']) -> name('logout');

    Route::get('/users', [UserController::class, 'getUsers']) -> name('users');
    Route::post('/users', [UserController::class, 'searchUser']) -> name('users');
    Route::get('/settings', [UserController::class, 'settings']) -> name('settings');
    Route::post('/settings', [UserController::class, 'updateSettings']) -> name('settings.save');

    Route::get('/chat/{uid?}', [ChatController::class, 'index']) -> name('chat');
    Route::post('/chat', [ChatController::class, 'sendMessage']) -> name('chat.save');

});
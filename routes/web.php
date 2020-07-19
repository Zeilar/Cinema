<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function() {
   return view('index');
})->name('index');


Route::post('/chat/is_not_typing', 'CommentController@isNotTyping')->name('chat_is_not_typing');
Route::post('/chat/is_typing', 'CommentController@isTyping')->name('chat_is_typing');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/comment/send', 'CommentController@store')->name('comment_send');
    Route::post('/room/create', 'RoomController@createRoom')->name('create_room');
    Route::post('/video/change', 'VideoController@change')->name('video_change');
    Route::post('/video/reset', 'VideoController@reset')->name('video_reset');
    Route::post('/video/pause', 'VideoController@pause')->name('video_pause');
    Route::get('/room/{id}', 'RoomController@enterRoom')->name('visit_room');
    Route::post('/video/play', 'VideoController@play')->name('video_play');
    Route::post('/video/sync', 'VideoController@sync')->name('video_sync');
});
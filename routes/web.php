<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:100,1', 'CreateUser'])->group(function() {
    Route::get('/', function() {
        $user = auth()->user();
        return view('index', [
            'rooms' => $user->rooms,
            'user' => $user,
        ]);
    })->name('index');
    Route::post('/room/create', 'RoomController@store')->name('room_create');
    Route::get('/room/{id}', 'RoomController@view')->name('room_enter');
});

Route::post('/chat/is_not_typing', 'CommentController@isNotTyping')->name('chat_is_not_typing');
Route::post('/chat/is_typing', 'CommentController@isTyping')->name('chat_is_typing');

Route::middleware('throttle:10,1')->group(function() {
    Route::post('/comment/delete', 'CommentController@destroy')->name('comment_delete');
    Route::post('/comment/send', 'CommentController@store')->name('comment_send');
    Route::post('/video/change', 'VideoController@change')->name('video_change');
    Route::post('/video/reset', 'VideoController@reset')->name('video_reset');
    Route::post('/video/pause', 'VideoController@pause')->name('video_pause');
    Route::post('/video/play', 'VideoController@play')->name('video_play');
    Route::post('/video/sync', 'VideoController@sync')->name('video_sync');
});
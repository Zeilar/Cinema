<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Events\NewComment;
use App\Comment;
use App\Video;
use App\User;

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
    // If user has no account, create one for them and log them in automatically
    if (!Auth::check()) {
        $r = rand(0, 127);
        $g = rand(0, 127);
        $b = rand(0, 127);

        $user = User::create([
            'username' => (new \Nubs\RandomNameGenerator\Alliteration())->getName(),
            'role' => 'viewer',
            'color' => "rgb($r, $g, $b)",
        ]);
        Auth::login($user);

        $comment = Comment::create([
            'user_id' => $user->id,
            'content' => 'has joined the chat',
        ]);
        $comment['color'] = $user->color;
        $comment['username'] = $user->username;

        broadcast(new NewComment($comment));
    }

    return view('index', [
        'comments' => Comment::all(),
        'videos' => Video::all(),
        'activeVideo' => Video::find(Cache::get('activeVideo')),
    ]);
});

Route::post('/chat/is_not_typing', 'CommentController@isNotTyping')->name('chat_is_not_typing');
Route::post('/chat/is_typing', 'CommentController@isTyping')->name('chat_is_typing');
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/comment/send', 'CommentController@store')->name('comment_send');
    Route::post('/video/change', 'VideoController@change')->name('video_change');
    Route::post('/video/reset', 'VideoController@reset')->name('video_reset');
    Route::post('/video/pause', 'VideoController@pause')->name('video_pause');
    Route::post('/video/play', 'VideoController@play')->name('video_play');
    Route::post('/video/sync', 'VideoController@sync')->name('video_sync');
});
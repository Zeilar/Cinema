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
        $enum = DB::select("SELECT COLUMN_TYPE AS colors
            FROM
            INFORMATION_SCHEMA.COLUMNS
            WHERE
            TABLE_SCHEMA = 'cinema' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'color'
        ");

        $colors = $enum[0]->colors;
        $colors = substr_replace($colors, '', 0, 5);
        $colors = substr_replace($colors, '', strpos($colors, ')'), 1);
        $colors = explode(',', $colors);

        $user = User::create([
            'username' => new \Nubs\RandomNameGenerator\Alliteration(),
            'role' => 'viewer',
            'color' => array_rand($colors),
        ]);
        Auth::login($user);

        $broadcast = true;
    }

    $online_users = Cache::get('online_users') ?? [];

    if (!in_array(auth()->user(), $online_users)) array_push($online_users, auth()->user());

    Cache::put('online_users', $online_users, 60 * 60 * 24);

    return view('index', [
        'comments' => Comment::all(),
        'videos' => Video::all(),
        'online_users' => collect(Cache::get('online_users')),
        'broadcast' => $broadcast ?? false,
    ]);
});

Route::post('/chat/push_status', 'CommentController@push_status')->name('push_status');
Route::post('/comment/send', 'CommentController@store')->name('comment_send');
Route::post('/video/change', 'VideoController@change')->name('video_change');
Route::post('/video/reset', 'VideoController@reset')->name('video_reset');
Route::post('/video/pause', 'VideoController@pause')->name('video_pause');
Route::post('/video/play', 'VideoController@play')->name('video_play');
Route::post('/video/sync', 'VideoController@sync')->name('video_sync');
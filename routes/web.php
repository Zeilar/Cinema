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

Route::get('/', function () {
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

        $user = \App\User::create([
            'username' => new \Nubs\RandomNameGenerator\Alliteration(),
            'role' => 'viewer',
            'color' => array_rand($colors),
        ]);
        Auth::login($user);
    }

    return view('index', [
        'comments' => \App\Comment::all(),
        'videos' => \App\Video::all(),
    ]);
});

Route::post('/comment/send', 'CommentController@store')->name('comment_post');
Route::post('/video/change', 'VideoController@change')->name('video_change');
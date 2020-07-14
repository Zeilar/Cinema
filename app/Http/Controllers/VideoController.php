<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Events\ChangeVideo;
use App\Events\NewComment;
use App\Comment;
use App\Video;
use Auth;

class VideoController extends Controller
{
    public function store(Request $request, Video $video) {
        //
    }

    public function change(Request $request) {
        if (is_null($request->video)) return response()->json(['error' => 'Something went wrong, refresh and try again']);

        $video = Video::find($request->video);

        if (!$video) return response()->json(['error' => 'That video does not exist, try another one']);

        broadcast(new ChangeVideo($video))->toOthers();
        if (Auth::check()) {
            $user = auth()->user();
            broadcast(new NewComment(Comment::create([
                'user_id' => $user->id,
                'username' => $user->username,
                'color' => $user->color,
                'content' => "Changed video to $video->title",
            ])));
        }

        Cache::put('activeVideo', $video->id);

        return response()->json([
            'video' => $video,
            'user' => auth()->user(),
        ]);
    }
}
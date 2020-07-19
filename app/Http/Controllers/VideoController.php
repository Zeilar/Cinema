<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Events\ChangeVideo;
use App\Events\NewComment;
use App\Events\VideoReset;
use App\Events\ConsoleLog;
use App\Events\VideoPause;
use App\Events\VideoPlay;
use App\Events\VideoSync;
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

        if (empty($video)) return response()->json(['error' => 'That video does not exist, try another one']);

        broadcast(new ChangeVideo($video))->toOthers();
        if (Auth::check()) {
            $user = auth()->user();
            broadcast(new NewComment(Comment::create([
                'user_id' => $user->id,
                'username' => $user->username,
                'color' => $user->color,
                'content' => "Changed video to $video->title",
            ])), $user, $request->roomId);
        }

        Cache::add('activeVideo', $video->id);

        return response()->json([
            'video' => $video,
            'user' => auth()->user(),
        ]);
    }

    public function play(Request $request) {
        broadcast(new ConsoleLog(auth()->user()->username, 'played the video', $request->roomId));
        broadcast(new VideoPlay($request->roomId));
    }

    public function sync(Request $request) {
        broadcast(new ConsoleLog(auth()->user()->username, 'synced the video', $request->roomId));
        broadcast(new VideoSync($request->timestamp));
    }

    public function reset(Request $request) {
        broadcast(new ConsoleLog(auth()->user()->username, 'reset the video', $request->roomId));
        broadcast(new VideoReset($request->roomId));
    }

    public function pause(Request $request) {
        broadcast(new ConsoleLog(auth()->user()->username, 'paused the video', $request->roomId));
        broadcast(new VideoPause($request->roomId));
    }
}
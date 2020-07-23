<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Events\Notification;
use App\Events\ChangeVideo;
use App\Events\NewComment;
use App\Events\VideoReset;
use App\Events\VideoPause;
use App\Events\VideoPlay;
use App\Events\VideoSync;
use App\Comment;
use App\Video;
use App\Room;
use Auth;

class VideoController extends Controller
{
    public function store(Request $request, Video $video) {
        //
    }

    public function change(Request $request) {
        if (!Auth::check()) return response()->json(['error' => 'Something went wrong, refresh and try again']);

        $user = auth()->user();
        $user->isOwner = $user->isOwner(Room::where('uuid', $request->roomUuid)->first());

        broadcast(new NewComment(Comment::create([
            'user_id' => $user->id,
            'content' => 'changed video',
        ]), $user, $request->roomUuid));

        broadcast(new ChangeVideo([
            'type' => $request->type,
            'url' => $request->url,
        ], $user, $request->roomUuid));
    }

    public function play(Request $request) {
        broadcast(new Notification(auth()->user()->username, $request->roomUuid, 'played the video', $request->type));
        broadcast(new VideoPlay($request->roomUuid));
    }

    public function sync(Request $request) {
        broadcast(new Notification(auth()->user()->username, $request->roomUuid, 'synced the video', $request->type));
        broadcast(new VideoSync($request->timestamp));
    }

    public function reset(Request $request) {
        broadcast(new Notification(auth()->user()->username, $request->roomUuid, 'reset the video', $request->type));
        broadcast(new VideoReset($request->roomUuid));
    }

    public function pause(Request $request) {
        broadcast(new Notification(auth()->user()->username, $request->roomUuid, 'paused the video', $request->type));
        broadcast(new VideoPause($request->roomUuid));
    }
}
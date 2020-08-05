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
use App\Events\VideoTime;
use App\Comment;
use App\Video;
use App\Room;
use Auth;

class VideoController extends Controller
{
    public function change(Request $request) {
        if (!Auth::check()) return response()->json(['error' => 'Something went wrong, refresh and try again']);

        $user = auth()->user();
        $user->isRoomOwner = $user->isRoomOwner(Room::find($request->roomId));

        broadcast(new NewComment(Comment::create([
            'content' => 'changed video',
            'user_id' => $user->id,
        ]), $user, $request->roomId));

        broadcast(new ChangeVideo([
            'id' => $request->id,
        ], $user, $request->roomId));
    }

    public function play(Request $request) {
        broadcast(new Notification(auth()->user(), $request->roomId, 'played the video', $request->type));
        broadcast(new VideoPlay($request->roomId));
    }

    public function sync(Request $request) {
        broadcast(new Notification(auth()->user(), $request->roomId, 'changed the video time', $request->type));
        broadcast(new VideoTime($request->timestamp, $request->roomId));
    }

    public function reset(Request $request) {
        broadcast(new Notification(auth()->user(), $request->roomId, 'reset the video', $request->type));
        broadcast(new VideoReset($request->roomId));
    }

    public function pause(Request $request) {
        broadcast(new Notification(auth()->user(), $request->roomId, 'paused the video', $request->type));
        broadcast(new VideoPause($request->roomId));
    }
}
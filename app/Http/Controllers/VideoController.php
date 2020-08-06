<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Events\Notification;
use App\Events\NewComment;
use App\Events\VideoReset;
use App\Events\VideoPause;
use App\Events\VideoPlay;
use App\Events\VideoTime;
use App\Events\AddVideo;
use App\Comment;
use App\Video;
use App\Room;
use Auth;

class VideoController extends Controller
{
    public function add(Request $request) {
        $room = Room::findOrFail($request->roomId);
        $playlist = $room->playlist();
        array_push($playlist, $request->videoId);
        $room->update(['playlist' => json_encode($playlist)]);

        broadcast(new AddVideo($request->videoId, auth()->user(), $request->roomId));
    }

    public function play(Request $request) {
        broadcast(new Notification(auth()->user(), $request->roomId, 'played the video', $request->type));
        broadcast(new VideoPlay($request->roomId));
    }

    public function changeTime(Request $request) {
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewComment;
use App\Comment;
use App\User;
use App\Room;
use Auth;

class RoomController extends Controller
{
    public function createRoom(Request $request) {
        $room = factory(Room::class)->create();
        $user = auth()->user();
        $comment = Comment::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'content' => 'has joined the chat',
        ]);
        $user->rooms()->syncWithoutDetaching($room);
        return redirect(route('room_enter', $room->anonymous_id));
    }

    public function enterRoom(Request $request, string $id) {
        $room = Room::where('anonymous_id', $request->id)->first();
        if (!$room) return redirect(route('index'));

        auth()->user()->rooms()->syncWithoutDetaching($room);

        return view('room', [
            'id' => $room->anonymous_id,
            'comments' => $room->comments,
            'videos' => $room->videos,
            'activeVideo' => $room->activeVideo(),
        ]);
    }
}
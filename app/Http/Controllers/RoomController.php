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
    public function store(Request $request) {
        $this->authorize('create', Comment::class);
        $user = auth()->user();
        $room = factory(Room::class)->create(['owner_id' => $user->id]);
        $user->rooms()->syncWithoutDetaching($room);
        return redirect(route('room_enter', $room->anonymous_id));
    }

    public function view(Request $request, string $id) {
        $room = Room::where('anonymous_id', $request->id)->first();
        if (!$room) return redirect(route('index'));
        $this->authorize('view', $room);

        auth()->user()->rooms()->syncWithoutDetaching($room);

        return view('room', [
            'room'        => $room,
            'id'          => $room->anonymous_id,
            'comments'    => $room->comments,
            'videos'      => $room->videos,
            'activeVideo' => $room->activeVideo(),
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewComment;
use Carbon\Carbon;
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
        return redirect(route('room_enter', $room->uuid));
    }

    public function view(Request $request, string $id) {
        $room = Room::where('uuid', $request->id)->first();
        if (!$room) return abort(404);
        $this->authorize('view', $room);
        setcookie('lastRoom', $room->id, Carbon::now()->addYear()->timestamp, '/');
        $user = auth()->user();
        $user->rooms()->syncWithoutDetaching($room);

        return view('room', [
            'room'        => $room,
            'activeVideo' => $room->activeVideo(),
        ]);
    }
}
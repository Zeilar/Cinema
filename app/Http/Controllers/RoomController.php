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
        $this->authorize('create', Room::class);

        $request->validate([
            'roomName' => 'unique:rooms,name|max:20',
        ]);

        $user = auth()->user();
        $room = ['owner_id' => $user->id];
        if (isset($request->roomName)) $room['name'] = $request->roomName;
        $room = factory(Room::class)->create($room);
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
            'room' => $room,
        ]);
    }
}
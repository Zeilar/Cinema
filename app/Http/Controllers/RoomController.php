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
        $room = Room::create();
        $user = auth()->user();
        if (!Auth::check()) {
            $user = factory(User::class)->create();
            Auth::login($user);
        }
        $comment = Comment::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'content' => 'has joined the chat',
        ]);
        $comment['color'] = $user->color;
        $comment['username'] = $user->username;
        broadcast(new NewComment($comment));
        return redirect(route('visit_room', $room->id));
    }

    public function visitRoom(Request $request, int $id) {
        $room = Room::find($request->id);
        if (!$room) return redirect(route('index'));

        if (!Auth::check()) {
            $user = factory(User::class)->create();
            Auth::login($user);
        }

        return view('room', [
            'id' => $room->id,
            'comments' => $room->comments,
            'videos' => $room->videos,
            'activeVideo' => $room->activeVideo(),
        ]);
    }
}
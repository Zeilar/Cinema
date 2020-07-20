<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\IsNotTyping;
use App\Events\NewComment;
use App\Events\ConsoleLog;
use App\Events\IsTyping;
use App\Comment;
use App\Room;
use Auth;

class CommentController extends Controller
{
    public function store(Request $request) {
        if (is_null($request->content)) return response()->json(['error' => 'Something went wrong, refresh and try again']);

        $user = auth()->user();
        $user->isOwner = $user->isOwner(Room::where('anonymous_id', $request->roomId)->first());

        $comment = Comment::create([
            'user_id' => $user->id,
            'room_id' => Room::where('anonymous_id', $request->roomId)->first()->id,
            'content' => $request->content,
        ]);

        broadcast(new NewComment($comment, $user, $request->roomId));
    }

    public function isTyping(Request $request) {
        broadcast(new IsTyping(auth()->user(), $request->roomId));
    }

    public function isNotTyping(Request $request) {
        if (!Auth::check()) return response()->json(['error' => 'Something went wrong, refresh and try again']);
        broadcast(new IsNotTyping(auth()->user(), $request->roomId));
    }
}
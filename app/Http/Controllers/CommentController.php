<?php

namespace App\Http\Controllers;

use App\Events\DeleteComment;
use App\Events\Notification;
use Illuminate\Http\Request;
use App\Events\IsNotTyping;
use App\Events\NewComment;
use App\Events\IsTyping;
use App\Comment;
use App\Room;
use Auth;

class CommentController extends Controller
{
    public function store(Request $request) {
        $this->authorize('create', Comment::class);
        if (is_null($request->content)) return response()->json(['error' => 'Something went wrong, refresh and try again']);

        $user = auth()->user();
        $room = Room::find($request->roomId);
        $user->isRoomOwner = $user->isRoomOwner($room);

        $comment = Comment::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'content' => $request->content,
        ]);
        $comment->timestamp = commentTimeFormat($comment->created_at);

        broadcast(new NewComment($comment, $user, $request->roomId));
    }

    public function destroy(Request $request) {
        $comment = Comment::find($request->id);
        $this->authorize('delete', $comment);
        $comment->delete();
        broadcast(new DeleteComment($comment->id, $request->roomId));
    }

    public function isTyping(Request $request) {
        broadcast(new IsTyping(auth()->user(), $request->roomId));
    }

    public function isNotTyping(Request $request) {
        if (!Auth::check()) return response()->json(['error' => 'Something went wrong, refresh and try again']);
        broadcast(new IsNotTyping(auth()->user(), $request->roomId));
    }
}
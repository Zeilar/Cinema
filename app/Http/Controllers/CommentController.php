<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewComment;
use App\Comment;
use Auth;

class CommentController extends Controller
{
    public function index() {
        return view('comments', ['comments' => Comment::all()]);
    }

    public function store(Request $request, Comment $comment) {
        if (!Auth::check() || is_null($request->content)) {
            return response()->json(['error' => 'Something went wrong, refresh and try again']);
        }

        $user = auth()->user();
        $comment = $comment->create([
            'user_id' => $user->id,
            'username' => $user->username,
            'color' => $user->color,
            'content' => $request->content,
        ]);

        broadcast(new NewComment($comment))->toOthers();

        return response()->json([
            'comment' => $comment,
            'user' => $user,
        ]);
    }
}
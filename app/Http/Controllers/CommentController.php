<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewComment;
use App\Comment;

class CommentController extends Controller
{
    public function index() {
        return view('comments', ['comments' => Comment::all()]);
    }

    public function store(Request $request, Comment $comment) {
        $comment = $comment->create([
            'user_id' => 1,
            'content' => $request->content,
        ]);

        event(new NewComment($comment));

        return response()->json($comment);
    }
}
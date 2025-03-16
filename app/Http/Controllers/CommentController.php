<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $id) {
        $this->validate($request, ['content' => 'required']);
        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'post_id' => $id,
        ]);
        return response()->json($comment);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found!',
            ]);
        }

        return response()->json([
            'comments' => $post->comments()->with('user:id,name,image')->get(),
        ]);
    }

    public function store(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found!',
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'comment created!',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'comment not found!',
            ], 403);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'permission denied!',
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);

        return response()->json([
            'message' => 'comment updated!',
        ], 200);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'comment not found!',
            ], 403);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'permission denied!',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'comment deleted!',
        ], 200);
    }
}

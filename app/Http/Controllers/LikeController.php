<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found!',
            ], 403);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        //if not liked then like
        if(!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id,
            ]);

            return response()->json([
                'message' => 'Post Liked!',
            ], 200);
        }

        //else then delete
        $like->delete();
        return response()->json([
            'message' => 'Post unLiked!',
        ], 200);

    }
}

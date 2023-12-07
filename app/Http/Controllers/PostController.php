<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
        ->with('likes', function($like){
            return $like->where('user_id', auth()->user()->id)
            ->select('id','user_id','post_id')->get();
        })
        ->get();
        return response()->json([
            'posts' => $posts,
        ], 200);
    }

    public function show($id)
    {
        $post = Post::where('id', $id)->withCount('comments', 'likes')->get();
        return response()->json([
            'post' => $post,
        ], 200);
    }

    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image,
        ]);

        return response()->json([
            'message' => 'post created!',
            'post' => $post,
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found!',
            ]);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'permission denied!',
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body' => $attrs['body'],
        ]);

        return response()->json([
            'message' => 'post updated!',
            'post' => $post,
        ], 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found!',
            ]);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'permission denied!',
            ]);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response()->json([
            'message' => 'post deleted!',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;


class PostController extends Controller
{

    public function index(Request $request)
    {
        $cacheKey = 'posts:' . md5(json_encode($request->all()));

        return Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Post::query();

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->filled('author')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->author . '%');
                });
            }
            return $query->paginate(10);
        });
    }



    public function store(Request $request) {
        $this->validate($request, [
            'title' => 'required|string',
            'content' => 'required',
            'category' => 'required|in:Technology,Lifestyle,Education',
        ]);
        $post = auth()->user()->posts()->create($request->all());
        return response()->json($post);
    }

    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->update($request->all());
        return response()->json($post);
    }

    public function destroy($id) {
        $post = Post::findOrFail($id);
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted']);
    }

}

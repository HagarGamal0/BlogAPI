<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

    public function search(Request $request) {

            $query = Post::query();

            // Search by title (if provided)
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            // Search by author name (if provided)
            if ($request->filled('author')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->author . '%');
                });
            }

            // Filter by category (if provided)
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Filter by date range (if provided)
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            // Return paginated results
            return response()->json($query->paginate(10));

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

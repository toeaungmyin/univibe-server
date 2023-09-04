<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\admin\PostCollection;
use App\Http\Resources\admin\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(12);
        return response()->json(new PostCollection($posts));
    }

    public function show(Post $post)
    {
        return response()->json(new PostResource($post));
    }

    public function delete(Post $post)
    {
        $user = $post->user;
        if (isset($post->photo)) {
            Storage::delete($post->photo_url);
        }

        $post->delete();

        return response()->json(['message' => 'post deleted successfully'], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.content', 'like', '%' . $query . '%')
            ->orWhere('users.username', 'like', '%' . $query . '%')
            ->paginate(20);

        return response()->json(new PostCollection($posts));
    }


}

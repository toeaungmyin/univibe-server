<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PostRequest;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(10);
        return response()->json([$posts]);
    }
    public function store(PostRequest $postRequest)
    {
        $post = Post::create([
            'user_id' => $postRequest->user()->id,
        ]);

        if ($postRequest->has('body')) {
            $post->body = $postRequest->body;
            $post->save();
        }
        if ($postRequest->has('photo')) {
            $photo = $postRequest->file('photo');
            $photoName = Carbon::now() . '_' . $photo->getClientOriginalName() . $photo->getClientOriginalExtension();
            $photo_path = 'uploads/photo';
            $photo->storeAs($photo_path, $photoName);
            $photo_url = $photo_path . '/' . $photoName;
            $post->photo = $photo_url;
            $post->save();
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 200);
    }

    public function update(Post $post, Request $request)
    {
        if ($request->has('body')) {
            $post->body = $request->body;
            $post->save();
        }

        if ($request->has('photo')) {

            if (isset($post->photo)) {
                Storage::delete($post->photo_url);
            }

            $photo = $request->file('photo');
            $photoName = Carbon::now() . '_' . $photo->getClientOriginalName() . $photo->getClientOriginalExtension();
            $photo_path = 'uploads/photo';
            $photo->storeAs($photo_path, $photoName);
            $photo_url = $photo_path . '/' . $photoName;
            $post->photo = $photo_url;
            $post->save();
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }
    public function delele(Post $post)
    {
        if (isset($post->photo)) {
            Storage::delete($post->photo_url);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}

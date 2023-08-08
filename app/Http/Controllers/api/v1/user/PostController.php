<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PostRequest;
use App\Http\Resources\user\PostCollection;
use App\Http\Resources\user\PostResource;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return response()->json(new PostCollection($posts));
    }
    public function store(Request $postRequest)
    {
        if (!$postRequest->has('content') && !$postRequest->has('image')) {
            return response()->json([
                'message' => ['Post creation failed. Please provide either the \'content\' or \'image\' to create a post.'],
            ], 422);
        }

        $post = new Post();

        $post->user_id = $postRequest->user()->id;

        if ($postRequest->has('content')) {
            $post->content = $postRequest->content;
        }

        if ($postRequest->hasFile('image')) {
            $post->image = $this->uploadImage($postRequest->file('image'));
        }

        if ($postRequest->has('audience')) {
            $post->audience = $postRequest->audience;
            $post->save();
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => new PostResource($post)
        ], 200);
    }

    private function uploadImage($photo)
    {
        $photoPath = $photo->store('uploads/images', 'public');
        return Storage::disk('public')->url($photoPath);
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

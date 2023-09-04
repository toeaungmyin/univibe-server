<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\user\PostCollection;
use App\Http\Resources\user\PostResource;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::user()->id);
        // Convert arrays to collections and filter by 'id'
        $followers_collection = collect($user->followers)->unique('id');
        $followings_collection = collect($user->followings)->unique('id');

        // Filter followings who are not in followers (you are not following them)
        $followings = $followings_collection->whereNotIn('id', $followers_collection->pluck('id'));

        // Find friends (mutual followings)
        $friends = $followers_collection->whereIn('id', $followings_collection->pluck('id'));

        $posts = Post::where(function ($query) use ($friends, $followings) {
            $query->whereIn('user_id', $friends->pluck('id'))
            ->orWhereIn('user_id', $followings->pluck('id'))->orWhere('user_id', Auth::user()->id);
        })
            ->whereIn('audience', ['public', 'friends'])
            ->latest()
            ->paginate(10);

        return response()->json(new PostCollection($posts));
    }

    public function getUserPosts(User $user)
    {
        $posts = $user->posts()->latest()->paginate(10);
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
        return $photoPath;
    }

    public function update(Post $post, Request $request)
    {




        if ($request->input('isImageRemove') !== 'true') {
            if ($request->has('image')) {

                if ($post->image) {
                    Storage::delete($post->image);
                }

                $post->image = $this->uploadImage($request->file('image'));
                $post->save();
            }
        } else {
            if ($post->image) {
                Storage::delete($post->image);
                $post->image = null;
                $post->save();
            }
        }

        $post->audience = $request->audience;
        $post->content = $request->content;
        $post->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => new PostResource($post),
        ], 200);
    }
    public function delete(Post $post)
    {
        if (isset($post->image)) {
            Storage::delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function reactToPost(Request $request, Post $post)
    {
        // Get the authenticated user (assuming you're using authentication)
        $user = Auth::user();

        // Check if the user has already reacted to this post
        $existingReaction = Reaction::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingReaction) {
            // Delete the existing reaction (unreact)
            $existingReaction->delete();
            return response()->json(
                [
                    'post' => new PostResource($post),
                    'message' => 'Reaction removed successfully'
                ]
            );
        } else {
            // Create a new reaction
            $reaction = new Reaction([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

            $reaction->save();
            return response()->json([
                'post' => new PostResource($post),
                'message' => 'Reaction added successfully'
            ]);
        }
    }



    public function report(Request $request, Post $post)
    {
        // Assuming you have an authenticated admin user who issues the warning
        $user = User::find(Auth::user()->id);

        // Validate the warning title and description using Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        // If validation fails, return a JSON response with error messages
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        // Issue the warning using the WarningUser model method
        PostReport::create([
            'compliant_id' => $user->id,
            'resistant_id' => $post->user->id,
            'post_id' => $post->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        // Return a JSON response with success message
        return response()->json([
            'message' => 'Report has been sent successfully'
        ]);
    }
}

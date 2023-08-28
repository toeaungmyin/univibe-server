<?php

namespace App\Http\Controllers\api\v1\user;

use App\Events\CommentNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\user\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

use App\Notifications\NewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required',
        ]);

        // Create a new comment
        $comment = Comment::create([
            'user_id' => auth()->user()->id, // Assuming you have user authentication
            'post_id' => $validatedData['post_id'],
            'comment' => $validatedData['comment'],
        ]);


        $post = Post::find($validatedData['post_id']);

        $postOwner = $post->user;

        $commentUserIds = $post->comments->pluck('user_id')->unique()->except($postOwner->id)->toArray();

        if (auth()->user()->id !== $postOwner->id) {
            $postOwner->notify(new NewComment(auth()->user(), $post));
        }

        if (!empty($commentUserIds)) {
            $users = User::whereIn('id', $commentUserIds)->get();
            Notification::send($users, new NewComment(Auth::user(), $post));
        }

        return response()->json([
            'message' => 'Comment created successfully', 'comment' => new CommentResource($comment)
        ]);
    }

    // Update a comment
    public function update(Request $request, Comment $comment)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'required',
        ]);

        // Check if the user has permission to update the comment (e.g., the comment belongs to the user)
        if ($comment->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update the comment
        $comment->update(['comment' => $validatedData['comment']]);

        return response()->json([
            'message' => 'Comment updated successfully', 'comment' => new CommentResource($comment)
        ]);
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        // Check if the user has permission to delete the comment (e.g., the comment belongs to the user)
        if ($comment->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete the comment
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}

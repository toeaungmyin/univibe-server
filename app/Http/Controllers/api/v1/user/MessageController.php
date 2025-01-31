<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\user\ConversationsResource;
use App\Http\Resources\user\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve conversations for the user where user1_id or user2_id is the authenticated user's ID
        $conversations = Conversation::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id);
        })->get();

        // You can customize the response format as needed
        return response()->json(['conversations' => ConversationsResource::collection($conversations)]);
    }

    public function listMessages($selectedUserId)
    {
        $authUser = Auth::user();

        $conversation = Conversation::where(function ($query) use ($authUser, $selectedUserId) {
            $query->where('user1_id', $authUser->id)->where('user2_id', $selectedUserId)
                ->orWhere('user1_id', $selectedUserId)->where('user2_id', $authUser->id);
        })->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('conversation_id', $conversation->id)->take(50)->get();

        return response()->json(['messages' => MessageResource::collection($messages)]);
    }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id', // Check if sender_id exists in users table
            'receiver_id' => 'required|exists:users,id', // Check if receiver_id exists in users table
            'content' => 'required|string',
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create a new conversation if one doesn't exist between sender and receiver
        $senderId = $request->input('sender_id');
        $receiverId = $request->input('receiver_id');

        // Check if the conversation already exists for sender and receiver IDs
        $conversation = Conversation::where(function ($query) use ($senderId, $receiverId) {
            $query->where('user1_id', $senderId)->where('user2_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('user1_id', $receiverId)->where('user2_id', $senderId);
        })->first();

        if (!$conversation) {
            // If the conversation doesn't exist, create a new one
            $conversation = Conversation::create([
                'user1_id' => $senderId,
                'user2_id' => $receiverId,
            ]);
        }

        // Create a new message and associate it with the conversation
        $message = new Message();
        $message->sender_id = $request->input('sender_id');
        $message->receiver_id = $request->input('receiver_id');
        $message->content = $request->input('content');
        $message->conversation_id = $conversation->id;
        $message->save();

        // You can customize the response format as needed
        return response()->json([
            'message' => new MessageResource($message)
        ], 200);
    }

    public function destroy(Message $message)
    {
        // Check if the user has permission to delete the comment (e.g., the comment belongs to the user)
        if ($message->sender_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 422);
        }

        // Delete the comment
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}

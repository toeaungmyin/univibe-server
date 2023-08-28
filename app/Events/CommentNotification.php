<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected $user;
    protected $post;
    protected $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $post)
    {
        $this->user = $user;
        $this->post = $post;
        $this->message = ['user' => $user, 'post' => $post, 'message' => $user . ' comment on your post'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Model.User.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'comment-on-post';
    }
}

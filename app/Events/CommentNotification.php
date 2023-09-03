<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CommentNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // public $id;
    public $message;

    public function __construct($message)
    {
        // $this->id = Str::uuid();
        $this->message = $message;
    }

    public function broadcastWith()
    {
        return ["message" => $this->message];
    }

    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}

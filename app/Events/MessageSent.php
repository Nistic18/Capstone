<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        // Passing Eloquent model (will be serialized)
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Private channel per user
        return new PrivateChannel("chat.{$this->message->receiver_id}");
    }
}
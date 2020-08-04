<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // move to ShouldBroadcast and add/fix queues
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Comment;

class NewComment implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $roomId;
    public $user;

    public function __construct($comment, $user, $roomId)
    {
        $this->comment = $comment;
        $this->roomId = $roomId;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room-' . $this->roomId);
    }
}
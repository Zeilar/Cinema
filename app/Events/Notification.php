<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $roomUuid;
    public $type;
    public $user;

    public function __construct($user, $roomUuid, $message, $type)
    {
        $this->message = $message;
        $this->roomUuid = $roomUuid;
        $this->type = $type;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room-' . $this->roomUuid);
    }
}
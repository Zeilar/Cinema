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

class IsTyping implements ShouldbroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomUuid;
    public $user;

    public function __construct($user, $roomUuid)
    {
        $this->roomUuid = $roomUuid;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room-' . $this->roomUuid);
    }
}
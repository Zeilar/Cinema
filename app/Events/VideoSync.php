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

class VideoSync implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $timestamp;
    public $roomUuid;

    public function __construct($timestamp, $roomUuid)
    {
        $this->timestamp = $timestamp;
        $this->roomUuid = $roomUuid;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room-' . $this->roomUuid);
    }
}
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Room;

Broadcast::channel('room-{id}', function($user, $id) {
    $room = Room::find($id);
    $user->isRoomOwner = $user->isRoomOwner($room);
    return ['user' => $user, 'activeVideo' => $room->activeVideo()];
});
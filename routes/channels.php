<?php

use Illuminate\Support\Facades\Broadcast;
use App\Room;

Broadcast::channel('room-{id}', function($user, $id) {
    $user->isRoomOwner = $user->isRoomOwner(Room::find($id));
    return ['user' => $user];
});
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Room;

Broadcast::channel('room-{id}', function($user, $id) {
    $user->isOwner = $user->isOwner(Room::where('uuid', $id)->first());
    return ['user' => $user];
});
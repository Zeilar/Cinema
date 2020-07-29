<?php

use Illuminate\Support\Facades\Cache;
use \Carbon\Carbon;
use App\User;

if (!function_exists('abbreviateName')) {
    function abbreviateName(string $name): string {
        $abbreviated = '';
        preg_match_all('([A-Z]+)', $name, $matches);
        foreach ($matches[0] as $letter) {
            $abbreviated .= $letter;
        }
        return $abbreviated;
    }
}

if (!function_exists('isOnline')) {
    function isOnline(object $user): bool {
        return Cache::get('user-online-' . $user->id) !== null ? true : false;
    }
}

if (!function_exists('getOnlineUsers')) {
    function getOnlineUsers(): object {
        $online_users = [];
        foreach (User::all() as $user) {
            if (Cache::has('user-online-' . $user->id)) array_push($online_users, $user);
        }
        return collect($online_users);
    }
}

if (!function_exists('commentTimeFormat')) {
    function commentTimeFormat($timestamp) {
        return Carbon::parse($timestamp)->setTimezone($_COOKIE['timezone'] ?? 'UTC')->format('H:i');
    }
}
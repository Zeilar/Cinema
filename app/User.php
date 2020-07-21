<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function rooms() {
        return $this->belongsToMany(Room::class);
    }

    public function owned_rooms() {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function isOwner(Room $room) {
        return $room->owner->id === $this->id;
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }
}
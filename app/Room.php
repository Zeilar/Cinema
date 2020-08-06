<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = [];

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function playlist() {
        return $this->playlist ? json_decode($this->playlist) : false;
    }

    public function activeVideo() {
        return empty($this->activeVideo) ? false : true;
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
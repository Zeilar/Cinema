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
        return $this->hasOne(Playlist::class);
    }

    public function activeVideo() {
        if (isset($this->activeVideo)) {
            $activeVideo = Video::find($this->activeVideo) ?? false;
        } else {
            $activeVideo = false;
        }
        return $activeVideo;
    }
}
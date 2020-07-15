<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public function isEmbed() {
        return $this->embed() ?? false;
    }

    public function isLocal() {
        return $this->path() ?? false;
    }
}
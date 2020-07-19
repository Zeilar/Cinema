<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Room;

$factory->define(Room::class, function (Faker $faker) {
    return [
        'anonymous_id' => Str::random(30),
    ];
});
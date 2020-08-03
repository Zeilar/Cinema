<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Room;

$factory->define(Room::class, function(Faker $faker) {
    // Nubs generator package has some exceptionally long names, pick a new one if we get one of those
    $name = (new \Nubs\RandomNameGenerator\Vgng())->getName();
    while (strlen($name) >= 20) {
        $name = (new \Nubs\RandomNameGenerator\Vgng())->getName();
    }

    return [
        'uuid' => (string)Str::uuid(),
        'name' => $name,
    ];
});
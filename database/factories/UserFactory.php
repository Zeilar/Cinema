<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function(Faker $faker) {
    $r = rand(0, 127);
    $g = rand(0, 127);
    $b = rand(0, 127);
    return [
        'username' => (new \Nubs\RandomNameGenerator\Alliteration())->getName(),
        'color' => "rgb($r, $g, $b)",
        'role' => 'viewer',
    ];
});
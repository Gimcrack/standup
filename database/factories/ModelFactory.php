<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'api_token' => str_random(60),
        'admin_flag' => 0
    ];
});

$factory->state(App\User::class, 'admin', function (Faker\Generator $faker) {
    return [
        'admin_flag' => 1
    ];
});

$factory->define(App\Client::class, function(Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
        'version' => '1.0.' . $faker->randomDigit . '.0'
    ];
});

$factory->define(App\LogEntry::class, function(Faker\Generator $faker) {
    return [
        'client_id' => function() use ($faker) {
            return factory(App\Client::class)->create()->id;
        },
        'action' => $faker->word,
        'status' => $faker->sentence
    ];
});

$factory->define(App\MatchedFile::class, function(Faker\Generator $faker) {
    return [
        'client_id' => function() use ($faker) {
            return factory(App\Client::class)->create()->id;
        },
        'pattern_id' => function() use ($faker) {
            return factory(App\Pattern::class)->create()->id;
        },
        'file' => str_replace(" ","\\",$faker->sentence),
        'muted_flag' => 0
    ];
});

$factory->state(App\MatchedFile::class, 'muted', function(Faker\Generator $faker) {
    return [
        'client_id' => function() use ($faker) {
            return factory(App\Client::class)->create()->id;
        },
        'pattern_id' => function() use ($faker) {
            return factory(App\Pattern::class)->create()->id;
        },
        'file' => $faker->sentence,
        'muted_flag' => 1
    ];
});

$factory->define(App\ClientPasswordReset::class, function(Faker\Generator $faker) {
    return [
        'client_id' => function() use ($faker) {
            return factory(App\Client::class)->create()->id;
        },
    ];
});



$factory->define(App\Pattern::class, function(Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});

$factory->define(App\Chat::class, function(Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class)->create()->id,
        'message' => $faker->sentence
    ];
});

$factory->state(App\Pattern::class, 'unpublished', function ($faker) {
    return [
        'name' => $faker->unique()->word,
        'published_flag' => 0
    ];
});

$factory->state(App\Pattern::class, 'published', function ($faker) {
    return [
        'name' => $faker->unique()->word,
        'published_flag' => 1
    ];
});


$factory->define(App\Exemption::class, function(Faker\Generator $faker) {
    return [
        'pattern' => $faker->unique()->word
    ];
});


$factory->state(App\Exemption::class, 'unpublished', function ($faker) {
    return [
        'pattern' => $faker->unique()->word,
        'published_flag' => 0
    ];
});

$factory->state(App\Exemption::class, 'published', function ($faker) {
    return [
        'pattern' => $faker->unique()->word,
        'published_flag' => 1
    ];
});

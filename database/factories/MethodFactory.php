<?php

use App\Models\Method;
use Faker\Generator as Faker;

$factory->define(Method::class, function (Faker $faker) {
    return [
        'Name' => $faker->randomElement(['HTML', 'CSS', 'PHP', 'VueJS', 'ReactJS', 'React Native', 'NodeJS'])
    ];
});

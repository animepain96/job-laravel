<?php

use App\Models\Job;
use Faker\Generator as Faker;

$factory->define(Job::class, function (Faker $faker) {
    return [
        'Name' => $faker->jobTitle,
        'CustomerID' => $faker->numberBetween(1,100),
        'TypeID' => $faker->numberBetween(1,100),
        'MethodID' => $faker->numberBetween(1,100),
        'StartDate' => $faker->dateTimeThisMonth,
        'RealJob' => true,
        'Deadline' => $faker->dateTimeThisMonth,
        'Price' => $price = $faker->numberBetween(100, 10000),
        'PriceYen' => $price * 100,
        'Paydate' => $faker->dateTimeThisMonth,
        'FinishDate' => $faker->dateTimeThisMonth,
        'Paid' => $faker->boolean
    ];
});

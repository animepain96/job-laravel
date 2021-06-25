<?php

namespace Database\Factories;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->jobTitle,
            'customer_id' => $this->faker->randomNumber(1,5),
            'method_id' => $this->faker->randomNumber(1,5),
            'type_id' => $this->faker->randomNumber(1,5),
            'start_date' => $this->faker->dateTimeThisMonth,
            'pay_date' => $this->faker->dateTimeThisMonth,
            'price' => $this->faker->numberBetween(1000,100000),
            'price_yen' => $this->faker->numberBetween(1000,100000),
        ];
    }
}

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Promo;
use App\Enums\PromoStatusEnum;

$factory->define(Promo::class, function (Faker $faker) {
    return [
        'promo_title' => $faker->unique()->word,
        'max_redeem' => rand(10, 50),
        'start_date' => $faker->dateTime(),
        'end_date' => $faker->dateTime(),
        'voucher_code' => $faker->unique()->word,
        'status' => (new PromoStatusEnum)->getRandomValue(),
        'term_cond' => $faker->text(),
        'galon' => rand(0, 20),
        'refill_galon' => rand(0, 20),
        '15lt' => rand(0, 20),
        '600ml' => rand(0, 20),
        '400ml' => rand(0, 20),
        'promo_thumbnail' => $faker->word,
        'promo_image' => $faker->word,
    ];
});

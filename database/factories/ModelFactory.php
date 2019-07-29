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

$factory->define(App\Usuario::class, function ($faker) {
    return [
        'usu_email' => $faker->unique()->safeEmail,
        'usu_nombre_usuario' => $faker->userName,
        'usu_password' => $faker->password,
        'usu_salt' => str_random(8),
        'usu_facebook_id' => str_random(8),
        'usu_twitter_id' => str_random(8),
        'usu_nombres' => $faker->firstName,
        'usu_apellido_paterno' => $faker->lastName,
        'usu_apellido_materno' => $faker->lastName,
        'usu_discapacidad' => $faker->boolean(50) ,
        'usu_tipo_discapacidad' => $faker->randomDigit
    ];
});

/**
 * Factory definition for model App\Region.
 */
$factory->define(App\Region::class, function ($faker) {
    return [
        'reg_nombre' => 'Region '.$faker->lastName,
        'reg_orden' => $faker->randomDigit
    ];
});

/**
 * Factory definition for model App\Provincia.
 */
$factory->define(App\Provincia::class, function ($faker) use ($factory) {
    return [
        'pro_reg_id' => $factory->create(App\Region::class)->reg_id,
        'pro_nombre' => $faker->state,
        'pro_orden' => $faker->randomDigit
    ];
});

/**
 * Factory definition for model App\Comuna.
 */
$factory->define(App\Comuna::class, function ($faker) use ($factory) {
    return [
        'com_pro_id' => $factory->create(App\Provincia::class)->pro_id,
        'com_nombre' => $faker->city,
        'com_orden' => $faker->randomDigit
    ];
});

/**
 * Factory definition for model App\PuntoEvaluacion.
 */
$factory->define(App\PuntoEvaluacion::class, function ($faker) use ($factory) {
    return [
        'pev_usu_id' => App\Usuario::inRandomOrder()->first()->usu_id,
        'pev_com_id' => App\Comuna::inRandomOrder()->first()->com_id,
        'pev_titulo' => $faker->streetName,
        'pev_tipo' => $faker->randomDigit,
        'pev_latitud' => $faker->latitude(-39.77,-39.85),
        'pev_longitud' => $faker->longitude(-73.2,-73.35),
        'pev_votos_positivos' => $faker->randomNumber,
        'pev_votos_negativos' => $faker->randomNumber,
        'pev_evaluacion_ponderada' => $faker->numberBetween(1,3)
    ];
});

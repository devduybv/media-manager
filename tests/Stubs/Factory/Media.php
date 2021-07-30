<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use VCComponent\Laravel\MediaManager\Entities\Collection;
use VCComponent\Laravel\MediaManager\Entities\Media;

$factory->define(Collection::class, function (Faker $faker) {
    $name = $faker->words(rand(4, 7), true);
    $slug = Str::slug($name);
    return [
        'name' => $name,
        'slug' => $slug,
        'description' => $faker->words(rand(4, 10), true),
        'order' => 1,
    ];
});

$factory->define(Media::class, function (Faker $faker) {
    return [
        'model_type' => $faker->randomElement(['categories', 'posts', 'products']),
        'model_id' => 1,
        'collection_name' => 'default',
        'name' => $faker->words(rand(1, 1), true),
        'file_name' => $faker->words(rand(1, 1), true),
        'alt_img' => $faker->words(rand(1, 1), true),
        'mime_type' => 'image/jpeg',
        'disk' => 'media',
        'size' => 1024,
        'manipulations' => 1,
        'custom_properties' => 1,
        'responsive_images' => 1,
        'order_column' => 1,
    ];
});

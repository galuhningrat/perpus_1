<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'title' => fake()->sentence(5),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'publication_year' => fake()->numberBetween(1990, 2024),
            'isbn' => fake()->isbn13(),
            'stock' => fake()->numberBetween(1, 5),
            'available_stock' => fake()->numberBetween(1, 5),
            'shelf_location' => Str::upper(fake()->bothify('??-###')),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}

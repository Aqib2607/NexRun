<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Running Shoes', 'Training Shoes', 'Casual Sneakers',
            'T-Shirts', 'Hoodies', 'Jackets', 'Shorts', 'Track Pants',
            'Sports Bras', 'Accessories', 'Socks', 'Caps & Hats',
        ]);

        return [
            'category_name' => $name,
            'slug'          => Str::slug($name),
            'description'   => fake()->sentence(),
            'status'        => 'active',
            'sort_order'    => fake()->numberBetween(0, 100),
        ];
    }
}

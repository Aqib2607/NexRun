<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true) . ' ' . fake()->randomElement(['Sneaker', 'Running Shoe', 'T-Shirt', 'Jacket', 'Shorts', 'Tracksuit']);
        $basePrice = fake()->randomFloat(2, 500, 15000);

        return [
            'sku'               => 'NR-' . strtoupper(Str::random(8)),
            'category_id'       => Category::factory(),
            'brand_id'          => Brand::factory(),
            'product_name'      => ucwords($name),
            'slug'              => Str::slug($name) . '-' . Str::random(5),
            'short_description' => fake()->sentence(10),
            'description'       => fake()->paragraphs(3, true),
            'base_price'        => $basePrice,
            'sale_price'        => fake()->boolean(30) ? round($basePrice * 0.8, 2) : null,
            'status'            => 'active',
            'featured'          => fake()->boolean(20),
            'gender'            => fake()->randomElement(['men', 'women', 'unisex', 'kids']),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn() => ['status' => 'draft']);
    }

    public function featured(): static
    {
        return $this->state(fn() => ['featured' => true]);
    }
}

<?php

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list active products with pagination', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    Product::factory()->count(20)->for($category)->for($brand)->create(['status' => 'active']);
    Product::factory()->count(5)->for($category)->for($brand)->create(['status' => 'draft']); // Should not be in response

    $response = $this->getJson('/api/v1/products');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     '*' => ['id', 'sku', 'name', 'base_price']
                 ],
                 'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                 'links'
             ]);

    $this->assertCount(15, $response->json('data')); // Default pagination is 15
    $this->assertEquals(20, $response->json('meta.total'));
});

test('can fetch a single product by slug', function () {
    $product = Product::factory()->create(['status' => 'active']);

    $response = $this->getJson('/api/v1/products/' . $product->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => ['id', 'name', 'slug', 'description']
             ])
             ->assertJsonPath('data.id', $product->id);
});

test('can filter products by category', function () {
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();

    $brand = Brand::factory()->create();

    Product::factory()->count(3)->for($category1)->for($brand)->create(['status' => 'active']);
    Product::factory()->count(2)->for($category2)->for($brand)->create(['status' => 'active']);

    $response = $this->getJson('/api/v1/products?category_id=' . $category1->id);

    $response->assertStatus(200);
    $this->assertCount(3, $response->json('data'));
    $this->assertEquals(3, $response->json('meta.total'));
});

test('can search products by name', function () {
    Product::factory()->create(['product_name' => 'Nike Air Max', 'status' => 'active']);
    Product::factory()->create(['product_name' => 'Adidas Ultraboost', 'status' => 'active']);

    $response = $this->getJson('/api/v1/products/search?q=Nike');

    $response->assertStatus(200);
    $this->assertCount(1, $response->json('data'));
    $this->assertEquals('Nike Air Max', $response->json('data.0.name'));
});

test('search requires minimum characters', function () {
    $response = $this->getJson('/api/v1/products/search?q=N');

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['q']);
});

test('can fetch featured products', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    Product::factory()->count(5)->for($category)->for($brand)->create(['status' => 'active', 'featured' => true]);
    Product::factory()->count(10)->for($category)->for($brand)->create(['status' => 'active', 'featured' => false]);

    $response = $this->getJson('/api/v1/products/featured');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     '*' => ['id', 'name']
                 ]
             ]);

    $this->assertCount(5, $response->json('data'));
});

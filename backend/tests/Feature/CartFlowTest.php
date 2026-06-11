<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function createVariant() {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->for($category)->for($brand)->create(['status' => 'active', 'base_price' => 100.00]);

    $size = Size::firstOrCreate(['size_code' => 'M']);
    $color = Color::firstOrCreate(['hex_code' => '#FF0000'], ['color_name' => 'Red']);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'color_id' => $color->id,
        'variant_sku' => 'TEST-SKU-' . Str::random(5),
        'price_adjustment' => 0,
        'status' => 'active'
    ]);

    $warehouse = Warehouse::firstOrCreate(['warehouse_name' => 'Main'], ['location' => 'HQ']);
    Inventory::create([
        'warehouse_id' => $warehouse->id,
        'product_variant_id' => $variant->id,
        'quantity_available' => 100,
        'quantity_reserved' => 0,
        'reorder_level' => 10,
    ]);

    return $variant;
}

test('user can fetch their empty cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/cart');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => ['id', 'items', 'item_count', 'total']
             ]);

    $this->assertEquals(0, $response->json('data.item_count'));
    $this->assertEmpty($response->json('data.items'));
});

test('user can add item to cart', function () {
    $user = User::factory()->create();
    $variant = createVariant();

    $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
        'product_variant_id' => $variant->id,
        'quantity' => 2
    ]);

    $response->assertStatus(200);
    $this->assertEquals(2, $response->json('data.item_count'));
    $this->assertCount(1, $response->json('data.items'));
    $this->assertEquals($variant->id, $response->json('data.items.0.product_variant_id'));
    $this->assertEquals(2, $response->json('data.items.0.quantity'));
    $this->assertEquals(200, $response->json('data.total'));
});

test('user can update item quantity in cart', function () {
    $user = User::factory()->create();
    $variant = createVariant();

    $this->actingAs($user)->postJson('/api/v1/cart/items', [
        'product_variant_id' => $variant->id,
        'quantity' => 1
    ]);

    // Need to get cart to find item ID
    $cartResponse = $this->actingAs($user)->getJson('/api/v1/cart');
    $itemId = $cartResponse->json('data.items.0.id');

    $response = $this->actingAs($user)->putJson('/api/v1/cart/items/' . $itemId, [
        'quantity' => 5
    ]);

    $response->assertStatus(200);
    $this->assertEquals(5, $response->json('data.item_count'));
    $this->assertEquals(5, $response->json('data.items.0.quantity'));
    $this->assertEquals(500, $response->json('data.total'));
});

test('user can remove item from cart', function () {
    $user = User::factory()->create();
    $variant = createVariant();

    $this->actingAs($user)->postJson('/api/v1/cart/items', [
        'product_variant_id' => $variant->id,
        'quantity' => 1
    ]);

    $cartResponse = $this->actingAs($user)->getJson('/api/v1/cart');
    $itemId = $cartResponse->json('data.items.0.id');

    $response = $this->actingAs($user)->deleteJson('/api/v1/cart/items/' . $itemId);

    $response->assertStatus(200);
    $this->assertEquals(0, $response->json('data.item_count'));
    $this->assertEmpty($response->json('data.items'));
});

test('user can clear their cart', function () {
    $user = User::factory()->create();
    $variant1 = createVariant();
    $variant2 = createVariant();

    $this->actingAs($user)->postJson('/api/v1/cart/items', [
        'product_variant_id' => $variant1->id,
        'quantity' => 1
    ]);
    $this->actingAs($user)->postJson('/api/v1/cart/items', [
        'product_variant_id' => $variant2->id,
        'quantity' => 1
    ]);

    $response = $this->actingAs($user)->deleteJson('/api/v1/cart/clear');

    $response->assertStatus(200);

    $cartResponse = $this->actingAs($user)->getJson('/api/v1/cart');
    $this->assertEquals(0, $cartResponse->json('data.item_count'));
    $this->assertEmpty($cartResponse->json('data.items'));
});

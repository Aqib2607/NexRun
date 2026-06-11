<?php

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Color;
use App\Models\CustomerAddress;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function createVariantWithInventory($available = 100) {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->for($category)->for($brand)->create(['status' => 'active', 'base_price' => 100.00, 'sale_price' => null]);

    $size = Size::firstOrCreate(['size_code' => 'XL']);
    $color = Color::firstOrCreate(['hex_code' => '#0000FF'], ['color_name' => 'Blue']);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'color_id' => $color->id,
        'variant_sku' => 'ORD-SKU-' . Str::random(5),
        'price_adjustment' => 0,
        'status' => 'active'
    ]);

    $warehouse = Warehouse::firstOrCreate(['warehouse_name' => 'Main'], ['location' => 'HQ']);
    
    Inventory::create([
        'warehouse_id' => $warehouse->id,
        'product_variant_id' => $variant->id,
        'quantity_available' => $available,
        'quantity_reserved' => 0,
        'reorder_level' => 10,
    ]);

    return $variant;
}

function createAddress($userId) {
    return CustomerAddress::create([
        'customer_id' => $userId,
        'address_type' => 'shipping',
        'recipient_name' => 'John Doe',
        'phone' => '1234567890',
        'district' => 'Dhaka',
        'city' => 'Dhaka',
        'postal_code' => '1200',
        'address_line_1' => '123 Street',
    ]);
}

test('creates order successfully from cart', function () {
    $user = User::factory()->create();
    $variant = createVariantWithInventory(10);
    $address = createAddress($user->id);

    // Add item to cart
    app(CartService::class)->addItem($user, $variant->id, 2);

    $service = app(OrderService::class);
    $order = $service->createOrder($user, [
        'shipping_address_id' => $address->id,
        'billing_address_id' => $address->id,
    ]);

    $this->assertNotNull($order);
    $this->assertEquals(200, $order->subtotal);
    $this->assertEquals(300, $order->total_amount); // 200 + 100 shipping
    
    $order->refresh();
    $this->assertEquals('pending', $order->order_status);
    $this->assertCount(1, $order->items);
    $this->assertEquals(2, $order->items->first()->quantity);

    // Cart should be empty
    $cart = Cart::where('customer_id', $user->id)->first();
    $this->assertTrue($cart->items->isEmpty());

    // Inventory should be reserved
    $inventory = Inventory::where('product_variant_id', $variant->id)->first();
    $this->assertEquals(8, $inventory->quantity_available);
    $this->assertEquals(2, $inventory->quantity_reserved);
});

test('updates order status correctly and handles inventory side effects', function () {
    $user = User::factory()->create();
    $variant = createVariantWithInventory(10);
    $address = createAddress($user->id);

    app(CartService::class)->addItem($user, $variant->id, 2);
    $service = app(OrderService::class);
    $order = $service->createOrder($user, [
        'shipping_address_id' => $address->id,
    ]);

    $order->refresh();
    $service->updateOrderStatus($order, 'paid', $user);

    $order->refresh();
    $this->assertEquals('paid', $order->order_status);

    // Reserved stock should be decremented after paid
    $inventory = Inventory::where('product_variant_id', $variant->id)->first();
    $this->assertEquals(8, $inventory->quantity_available);
    $this->assertEquals(0, $inventory->quantity_reserved);
    
    // Status history recorded
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'new_status' => 'paid',
        'changed_by' => $user->id
    ]);
});

test('cancels order successfully and releases inventory', function () {
    $user = User::factory()->create();
    $variant = createVariantWithInventory(10);
    $address = createAddress($user->id);

    app(CartService::class)->addItem($user, $variant->id, 3);
    $service = app(OrderService::class);
    $order = $service->createOrder($user, [
        'shipping_address_id' => $address->id,
    ]);

    $order->refresh();
    $service->cancelOrder($order, $user);

    $order->refresh();
    $this->assertEquals('cancelled', $order->order_status);

    // Inventory released
    $inventory = Inventory::where('product_variant_id', $variant->id)->first();
    $this->assertEquals(10, $inventory->quantity_available);
    $this->assertEquals(0, $inventory->quantity_reserved);
});

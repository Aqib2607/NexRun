<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function createInventoryItem($available = 100, $reserved = 0) {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->for($category)->for($brand)->create(['status' => 'active', 'base_price' => 100.00]);

    $size = Size::firstOrCreate(['size_code' => 'L']);
    $color = Color::firstOrCreate(['hex_code' => '#00FF00'], ['color_name' => 'Green']);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'color_id' => $color->id,
        'variant_sku' => 'INV-SKU-' . Str::random(5),
        'price_adjustment' => 0,
        'status' => 'active'
    ]);

    $warehouse = Warehouse::firstOrCreate(['warehouse_name' => 'Main'], ['location' => 'HQ']);
    
    return Inventory::create([
        'warehouse_id' => $warehouse->id,
        'product_variant_id' => $variant->id,
        'quantity_available' => $available,
        'quantity_reserved' => $reserved,
        'reorder_level' => 10,
    ]);
}

test('reserve stock successfully reserves and logs transaction', function () {
    $inventory = createInventoryItem(100, 0);
    $service = new InventoryService();

    $service->reserveStock($inventory->product_variant_id, 10);

    $inventory->refresh();

    $this->assertEquals(90, $inventory->quantity_available);
    $this->assertEquals(10, $inventory->quantity_reserved);

    $this->assertDatabaseHas('inventory_transactions', [
        'inventory_id' => $inventory->id,
        'transaction_type' => 'reservation',
        'quantity' => -10,
        'new_balance' => 90
    ]);
});

test('reserve stock throws exception if insufficient stock', function () {
    $inventory = createInventoryItem(5, 0);
    $service = new InventoryService();

    $this->expectException(HttpException::class);
    $this->expectExceptionMessage('Insufficient stock');

    $service->reserveStock($inventory->product_variant_id, 10);
});

test('confirm reservations decreases reserved stock and logs sale', function () {
    $inventory = createInventoryItem(90, 10);
    $user = User::factory()->create();
    
    $order = Order::create([
        'customer_id' => $user->id,
        'order_number' => 'ORD-12345',
        'subtotal' => 100,
        'total_amount' => 100,
        'order_status' => 'processing',
        'payment_status' => 'completed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_variant_id' => $inventory->product_variant_id,
        'product_name' => 'Test Product',
        'variant_sku' => 'TEST-SKU-123',
        'quantity' => 10,
        'unit_price' => 100,
        'total_price' => 1000,
    ]);

    $service = new InventoryService();
    $service->confirmReservations($order);

    $inventory->refresh();

    $this->assertEquals(90, $inventory->quantity_available);
    $this->assertEquals(0, $inventory->quantity_reserved);

    $this->assertDatabaseHas('inventory_transactions', [
        'inventory_id' => $inventory->id,
        'transaction_type' => 'sale',
        'quantity' => -10,
        'new_balance' => 90
    ]);
});

test('release reservations increases available stock and decreases reserved', function () {
    $inventory = createInventoryItem(90, 10);
    $user = User::factory()->create();
    
    $order = Order::create([
        'customer_id' => $user->id,
        'order_number' => 'ORD-67890',
        'subtotal' => 100,
        'total_amount' => 100,
        'order_status' => 'cancelled',
        'payment_status' => 'failed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_variant_id' => $inventory->product_variant_id,
        'product_name' => 'Test Product',
        'variant_sku' => 'TEST-SKU-123',
        'quantity' => 10,
        'unit_price' => 100,
        'total_price' => 1000,
    ]);

    $service = new InventoryService();
    $service->releaseReservations($order);

    $inventory->refresh();

    $this->assertEquals(100, $inventory->quantity_available);
    $this->assertEquals(0, $inventory->quantity_reserved);

    $this->assertDatabaseHas('inventory_transactions', [
        'inventory_id' => $inventory->id,
        'transaction_type' => 'release',
        'quantity' => 10,
        'new_balance' => 100
    ]);
});

test('adjust stock updates available quantity and logs transaction', function () {
    $inventory = createInventoryItem(50, 0);
    $service = new InventoryService();

    $service->adjustStock($inventory->id, 25, 'Found extra stock in warehouse');

    $inventory->refresh();

    $this->assertEquals(75, $inventory->quantity_available);

    $this->assertDatabaseHas('inventory_transactions', [
        'inventory_id' => $inventory->id,
        'transaction_type' => 'adjustment',
        'quantity' => 25,
        'new_balance' => 75,
        'remarks' => 'Found extra stock in warehouse'
    ]);
});

<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->order = Order::factory()->create([
        'customer_id' => $this->user->id,
        'total_amount' => 500,
        'payment_status' => 'pending',
    ]);
    $this->method = PaymentMethod::factory()->create(['slug' => 'cod']);
});

test('a user can initiate payment for their order', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/payment/initiate', [
        'order_id' => $this->order->id,
        'payment_method_id' => $this->method->id,
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.method', $this->method->display_name);
});

test('a user cannot initiate payment for someone elses order', function () {
    $otherUser = User::factory()->create();
    Sanctum::actingAs($otherUser);

    $response = $this->postJson('/api/v1/payment/initiate', [
        'order_id' => $this->order->id,
        'payment_method_id' => $this->method->id,
    ]);

    $response->assertStatus(404); // firstOrFail() should throw 404 since it's filtered by customer_id
});

test('it prevents initiating payment for an already paid order', function () {
    Sanctum::actingAs($this->user);
    $this->order->update(['payment_status' => 'completed']);

    $response = $this->postJson('/api/v1/payment/initiate', [
        'order_id' => $this->order->id,
        'payment_method_id' => $this->method->id,
    ]);

    $response->assertStatus(422)
             ->assertJsonPath('message', 'Order is already paid.');
});

<?php

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->paymentService = app(PaymentService::class);
    $this->user = User::factory()->create();
    $this->order = Order::factory()->create([
        'customer_id' => $this->user->id,
        'total_amount' => 1000.50,
        'payment_status' => 'pending',
    ]);
});

test('it can initiate an SSLCommerz payment', function () {
    $method = PaymentMethod::factory()->create(['slug' => 'sslcommerz', 'display_name' => 'SSLCommerz']);

    $result = $this->paymentService->initiate($this->order, $method->id);

    expect($result)->toHaveKeys(['payment_id', 'transaction_ref', 'amount', 'currency', 'method', 'gateway_response']);
    expect($result['gateway_response']['type'])->toBe('redirect');
    expect($result['gateway_response']['url'])->toContain('sslcommerz.com');
    expect($this->order->refresh()->payment_status)->toBe('processing');
});

test('it can initiate a COD payment', function () {
    $method = PaymentMethod::factory()->create(['slug' => 'cod', 'display_name' => 'Cash on Delivery']);

    $result = $this->paymentService->initiate($this->order, $method->id);

    expect($result['gateway_response']['type'])->toBe('cod');
    expect($this->order->refresh()->payment_status)->toBe('pending');
});

test('it can confirm a payment', function () {
    $method = PaymentMethod::factory()->create(['slug' => 'sslcommerz']);
    $paymentInfo = $this->paymentService->initiate($this->order, $method->id);
    
    $payment = \App\Models\Payment::find($paymentInfo['payment_id']);

    $this->paymentService->confirmPayment($payment, ['gateway_txn_id' => '12345']);

    expect($payment->refresh()->payment_status)->toBe('completed');
    expect($this->order->refresh()->payment_status)->toBe('completed');
});

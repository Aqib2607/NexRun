<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;

class PaymentService
{
    public function initiate(Order $order, int $paymentMethodId): array
    {
        $method = PaymentMethod::findOrFail($paymentMethodId);
        $transactionRef = 'TXN-' . strtoupper(Str::random(16));

        $payment = Payment::create([
            'order_id'              => $order->id,
            'payment_method_id'     => $paymentMethodId,
            'transaction_reference' => $transactionRef,
            'amount'                => $order->total_amount,
            'currency'              => 'BDT',
            'payment_status'        => 'processing',
        ]);

        $order->update(['payment_status' => 'processing']);

        // Build gateway-specific redirect/initiation
        $gatewayResponse = match ($method->slug) {
            'sslcommerz' => $this->initiateSSLCommerz($order, $payment),
            'bkash'      => $this->initiateBkash($order, $payment),
            'nagad'      => $this->initiateNagad($order, $payment),
            'stripe'     => $this->initiateStripe($order, $payment),
            'paypal'     => $this->initiatePayPal($order, $payment),
            'cod'        => $this->initiateCOD($order, $payment),
            default      => ['type' => 'redirect', 'url' => '#'],
        };

        return [
            'payment_id'        => $payment->id,
            'transaction_ref'   => $transactionRef,
            'amount'            => (float) $payment->amount,
            'currency'          => $payment->currency,
            'method'            => $method->display_name,
            'gateway_response'  => $gatewayResponse,
        ];
    }

    public function confirmPayment(Payment $payment, array $gatewayData = []): void
    {
        $payment->update([
            'payment_status'        => 'completed',
            'gateway_transaction_id' => $gatewayData['gateway_txn_id'] ?? null,
            'gateway_response'      => $gatewayData,
            'paid_at'               => now(),
        ]);

        $order = $payment->order;
        $order->update(['payment_status' => 'completed']);

        // Transition order to paid
        app(OrderService::class)->updateOrderStatus($order, 'paid', null, 'Payment confirmed.');
    }

    public function failPayment(Payment $payment, array $gatewayData = []): void
    {
        $payment->update([
            'payment_status'   => 'failed',
            'gateway_response' => $gatewayData,
        ]);

        $payment->order->update(['payment_status' => 'failed']);
    }

    public function processRefund(Payment $payment, float $amount, string $reason): \App\Models\Refund
    {
        if ($amount > $payment->amount) {
            abort(422, 'Refund amount exceeds payment amount.');
        }

        $refund = $payment->refunds()->create([
            'refund_amount'  => $amount,
            'refund_reason'  => $reason,
            'refund_status'  => 'completed',
            'refunded_at'    => now(),
        ]);

        $totalRefunded = $payment->refunds()->where('refund_status', 'completed')->sum('refund_amount');

        if ($totalRefunded >= $payment->amount) {
            $payment->update(['payment_status' => 'refunded']);
            $payment->order->update(['payment_status' => 'refunded']);
        } else {
            $payment->update(['payment_status' => 'partially_refunded']);
            $payment->order->update(['payment_status' => 'partially_refunded']);
        }

        return $refund;
    }

    // ── Gateway Implementations ──────────────────────────

    private function initiateSSLCommerz(Order $order, Payment $payment): array
    {
        $config = config('nexrun.payments.sslcommerz');
        $baseUrl = $config['sandbox'] ? 'https://sandbox.sslcommerz.com' : 'https://securepay.sslcommerz.com';

        return [
            'type'        => 'redirect',
            'url'         => "{$baseUrl}/gwprocess/v4/api.php",
            'store_id'    => $config['store_id'],
            'tran_id'     => $payment->transaction_reference,
            'total_amount' => (float)$order->total_amount,
            'currency'    => 'BDT',
        ];
    }

    private function initiateBkash(Order $order, Payment $payment): array
    {
        return [
            'type'       => 'mobile_wallet',
            'provider'   => 'bkash',
            'payment_id' => $payment->transaction_reference,
            'amount'     => (float)$order->total_amount,
        ];
    }

    private function initiateNagad(Order $order, Payment $payment): array
    {
        return [
            'type'       => 'mobile_wallet',
            'provider'   => 'nagad',
            'payment_id' => $payment->transaction_reference,
            'amount'     => (float)$order->total_amount,
        ];
    }

    private function initiateStripe(Order $order, Payment $payment): array
    {
        return [
            'type'         => 'client_secret',
            'payment_id'   => $payment->transaction_reference,
            'amount'       => (int)($order->total_amount * 100),
            'currency'     => 'bdt',
            'publishable_key' => config('nexrun.payments.stripe.key'),
        ];
    }

    private function initiatePayPal(Order $order, Payment $payment): array
    {
        return [
            'type'       => 'redirect',
            'payment_id' => $payment->transaction_reference,
            'amount'     => (float)$order->total_amount,
        ];
    }

    private function initiateCOD(Order $order, Payment $payment): array
    {
        $payment->update([
            'payment_status' => 'pending',
            'gateway_response' => ['method' => 'cash_on_delivery'],
        ]);

        $order->update(['payment_status' => 'pending']);

        app(OrderService::class)->updateOrderStatus($order, 'paid', null, 'Cash on delivery order confirmed.');

        return ['type' => 'cod', 'message' => 'Pay when you receive your order.'];
    }
}

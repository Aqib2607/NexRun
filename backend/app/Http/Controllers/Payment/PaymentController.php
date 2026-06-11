<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function __construct(private PaymentService $paymentService) {}

    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id'          => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('customer_id', $request->user()->id)
            ->firstOrFail();

        if ($order->payment_status === 'completed') {
            return $this->error('Order is already paid.', 422);
        }

        $result = $this->paymentService->initiate($order, $data['payment_method_id']);

        return $this->success($result, 'Payment initiated.');
    }

    public function status(Payment $payment, Request $request): JsonResponse
    {
        if ($payment->order->customer_id !== $request->user()->id) {
            abort(403, 'Access denied.');
        }

        return $this->success([
            'payment_id'     => $payment->id,
            'status'         => $payment->payment_status,
            'amount'         => (float) $payment->amount,
            'currency'       => $payment->currency,
            'method'         => $payment->paymentMethod?->display_name,
            'transaction_ref' => $payment->transaction_reference,
            'paid_at'        => $payment->paid_at?->toIso8601String(),
        ]);
    }
}


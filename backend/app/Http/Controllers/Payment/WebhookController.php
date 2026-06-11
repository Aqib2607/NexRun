<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\ApiController;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends ApiController
{
    public function __construct(private PaymentService $paymentService) {}

    public function sslcommerz(Request $request): JsonResponse
    {
        $data = $request->all();
        $payment = Payment::where('transaction_reference', $data['tran_id'] ?? '')->first();

        if (!$payment) return $this->error('Transaction not found.', 404);

        if (($data['status'] ?? '') === 'VALID') {
            $this->paymentService->confirmPayment($payment, $data);
        } else {
            $this->paymentService->failPayment($payment, $data);
        }

        return $this->success(null, 'Webhook processed.');
    }

    public function bkash(Request $request): JsonResponse
    {
        $data = $request->all();
        $payment = Payment::where('transaction_reference', $data['paymentID'] ?? '')->first();

        if (!$payment) return $this->error('Transaction not found.', 404);

        if (($data['statusCode'] ?? '') === '0000') {
            $this->paymentService->confirmPayment($payment, $data);
        } else {
            $this->paymentService->failPayment($payment, $data);
        }

        return $this->success(null, 'Webhook processed.');
    }

    public function nagad(Request $request): JsonResponse
    {
        $data = $request->all();
        $payment = Payment::where('transaction_reference', $data['orderId'] ?? '')->first();

        if (!$payment) return $this->error('Transaction not found.', 404);

        if (($data['status'] ?? '') === 'Success') {
            $this->paymentService->confirmPayment($payment, $data);
        } else {
            $this->paymentService->failPayment($payment, $data);
        }

        return $this->success(null, 'Webhook processed.');
    }

    public function stripe(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $data = json_decode($payload, true);
        $event = $data['type'] ?? '';

        if ($event === 'payment_intent.succeeded') {
            $txnRef = $data['data']['object']['metadata']['transaction_reference'] ?? '';
            $payment = Payment::where('transaction_reference', $txnRef)->first();
            if ($payment) {
                $this->paymentService->confirmPayment($payment, $data['data']['object']);
            }
        }

        return $this->success(null, 'Webhook processed.');
    }

    public function paypal(Request $request): JsonResponse
    {
        $data = $request->all();
        $txnRef = $data['resource']['custom_id'] ?? '';
        $payment = Payment::where('transaction_reference', $txnRef)->first();

        if ($payment && ($data['event_type'] ?? '') === 'CHECKOUT.ORDER.APPROVED') {
            $this->paymentService->confirmPayment($payment, $data);
        }

        return $this->success(null, 'Webhook processed.');
    }
}

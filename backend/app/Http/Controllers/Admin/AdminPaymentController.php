<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPaymentController extends ApiController
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['order.customer', 'paymentMethod']);

        if ($request->filled('status')) $query->where('payment_status', $request->status);
        if ($request->filled('method')) $query->whereHas('paymentMethod', fn($q) => $q->where('slug', $request->method));

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 20)));
    }

    public function refund(Payment $payment, Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        $refund = $this->paymentService->processRefund($payment, $data['amount'], $data['reason']);

        return $this->success([
            'refund_id' => $refund->id,
            'amount'    => (float)$refund->refund_amount,
            'status'    => $refund->refund_status,
        ], 'Refund processed.');
    }
}

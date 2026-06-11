<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends ApiController
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['customer', 'items', 'latestPayment.paymentMethod']);

        if ($request->filled('status'))       $query->byStatus($request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('customer_id'))  $query->forCustomer($request->customer_id);
        if ($request->filled('from') && $request->filled('to')) $query->placedBetween($request->from, $request->to);

        return $this->paginated($query->recent()->paginate($request->get('per_page', 20)));
    }

    public function show(Order $order): JsonResponse
    {
        return $this->success($order->load([
            'customer', 'items.productVariant.product',
            'payments.paymentMethod', 'shippingAddress',
            'statusHistory.changedBy', 'coupon',
        ]));
    }

    public function updateStatus(Order $order, Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string',
            'note'   => 'nullable|string|max:500',
        ]);

        $order = $this->orderService->updateOrderStatus(
            $order, $data['status'], $request->user(), $data['note'] ?? null
        );

        return $this->success(['order_status' => $order->order_status], 'Status updated.');
    }
}

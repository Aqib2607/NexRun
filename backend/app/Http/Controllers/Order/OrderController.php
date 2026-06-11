<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::forCustomer($request->user()->id)
            ->with(['items.productVariant.product', 'latestPayment.paymentMethod'])
            ->recent()
            ->paginate($request->get('per_page', 15));

        $data = $orders->through(fn($order) => $this->formatOrder($order));
        return $this->paginated($data);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->user(), $request->validated());

        return $this->created(
            $this->formatOrder($order->load(['items.productVariant.product', 'latestPayment.paymentMethod', 'shippingAddress'])),
            'Order placed successfully.'
        );
    }

    public function show(Order $order, Request $request): JsonResponse
    {
        $this->authorizeOrder($order, $request);

        $order->load([
            'items.productVariant.product.primaryImage',
            'items.productVariant.size',
            'items.productVariant.color',
            'payments.paymentMethod',
            'shippingAddress',
            'billingAddress',
            'statusHistory.changedBy',
            'coupon',
        ]);

        return $this->success($this->formatOrderDetail($order));
    }

    public function cancel(Order $order, Request $request): JsonResponse
    {
        $this->authorizeOrder($order, $request);

        $this->orderService->cancelOrder($order, $request->user());

        return $this->success(null, 'Order cancelled.');
    }

    public function tracking(Order $order, Request $request): JsonResponse
    {
        $this->authorizeOrder($order, $request);

        $history = $order->statusHistory()
            ->with('changedBy')
            ->get()
            ->map(fn($h) => [
                'status'     => $h->new_status,
                'note'       => $h->note,
                'changed_by' => $h->changedBy?->full_name ?? 'System',
                'changed_at' => $h->changed_at?->toIso8601String(),
            ]);

        return $this->success([
            'order_number'  => $order->order_number,
            'current_status' => $order->order_status,
            'history'       => $history,
        ]);
    }

    private function authorizeOrder(Order $order, Request $request): void
    {
        if ($order->customer_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }
    }

    private function formatOrder($order): array
    {
        return [
            'id'              => $order->id,
            'order_number'    => $order->order_number,
            'subtotal'        => (float)$order->subtotal,
            'discount_amount' => (float)$order->discount_amount,
            'shipping_amount' => (float)$order->shipping_amount,
            'total_amount'    => (float)$order->total_amount,
            'payment_status'  => $order->payment_status,
            'order_status'    => $order->order_status,
            'items_count'     => $order->items->count(),
            'placed_at'       => $order->placed_at?->toIso8601String(),
        ];
    }

    private function formatOrderDetail($order): array
    {
        return array_merge($this->formatOrder($order), [
            'items' => $order->items->map(fn($item) => [
                'product_name' => $item->product_name,
                'variant_sku'  => $item->variant_sku,
                'size'         => $item->productVariant?->size?->size_code,
                'color'        => $item->productVariant?->color?->color_name,
                'image'        => $item->productVariant?->product?->primaryImage?->image_url,
                'quantity'     => $item->quantity,
                'unit_price'   => (float)$item->unit_price,
                'total_price'  => (float)$item->total_price,
            ]),
            'shipping_address' => $order->shippingAddress,
            'billing_address'  => $order->billingAddress,
            'payments' => $order->payments->map(fn($p) => [
                'method'  => $p->paymentMethod?->display_name,
                'amount'  => (float)$p->amount,
                'status'  => $p->payment_status,
                'paid_at' => $p->paid_at?->toIso8601String(),
            ]),
            'coupon' => $order->coupon ? [
                'code'  => $order->coupon->coupon_code,
                'type'  => $order->coupon->coupon_type,
                'value' => (float)$order->coupon->value,
            ] : null,
            'history' => $order->statusHistory->map(fn($h) => [
                'status'     => $h->new_status,
                'note'       => $h->note,
                'changed_at' => $h->changed_at?->toIso8601String(),
            ]),
        ]);
    }
}

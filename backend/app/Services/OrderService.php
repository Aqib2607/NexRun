<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private InventoryService $inventoryService,
        private LoyaltyService $loyaltyService,
    ) {}

    public function createOrder(User $user, array $data): Order
    {
        $cart = Cart::where('customer_id', $user->id)->with('items.productVariant.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            abort(422, 'Your cart is empty.');
        }

        return DB::transaction(function () use ($user, $data, $cart) {
            // Calculate subtotal
            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += $item->unit_price * $item->quantity;
            }

            // Apply coupon
            $discountAmount = 0;
            $coupon = null;
            $shippingAmount = 100; // Default shipping BDT

            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::byCode($data['coupon_code'])->first();
                if ($coupon && $coupon->isValid() && !$coupon->hasBeenUsedByCustomer($user->id)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    if ($coupon->coupon_type === 'free_shipping') {
                        $shippingAmount = 0;
                    }
                }
            }

            // Apply loyalty points redemption
            if (!empty($data['redeem_points'])) {
                $pointsDiscount = $this->loyaltyService->calculatePointsDiscount($user, $data['redeem_points']);
                $discountAmount += $pointsDiscount;
            }

            $totalAmount = max(0, $subtotal - $discountAmount + $shippingAmount);

            // Create order
            $order = Order::create([
                'order_number'        => Order::generateOrderNumber(),
                'customer_id'         => $user->id,
                'shipping_address_id' => $data['shipping_address_id'],
                'billing_address_id'  => $data['billing_address_id'] ?? $data['shipping_address_id'],
                'subtotal'            => $subtotal,
                'discount_amount'     => $discountAmount,
                'shipping_amount'     => $shippingAmount,
                'tax_amount'          => 0,
                'total_amount'        => $totalAmount,
                'coupon_id'           => $coupon?->id,
                'notes'               => $data['notes'] ?? null,
                'placed_at'           => now(),
            ]);

            // Create order items + reserve inventory
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_name'       => $cartItem->productVariant->product->product_name,
                    'variant_sku'        => $cartItem->productVariant->variant_sku,
                    'quantity'           => $cartItem->quantity,
                    'unit_price'         => $cartItem->unit_price,
                    'discount_amount'    => 0,
                    'total_price'        => $cartItem->unit_price * $cartItem->quantity,
                ]);

                $this->inventoryService->reserveStock($cartItem->product_variant_id, $cartItem->quantity);
            }

            // Record coupon usage
            if ($coupon) {
                CouponUsage::create([
                    'coupon_id'   => $coupon->id,
                    'customer_id' => $user->id,
                    'order_id'    => $order->id,
                ]);
                $coupon->increment('times_used');
            }

            // Redeem loyalty points
            if (!empty($data['redeem_points'])) {
                $this->loyaltyService->redeemPoints($user, $data['redeem_points'], $order);
            }

            // Record status history
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'new_status' => 'pending',
                'note'       => 'Order placed.',
            ]);

            // Clear cart
            $cart->items()->delete();

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $newStatus, ?User $changedBy = null, ?string $note = null): Order
    {
        if (!$order->canTransitionTo($newStatus)) {
            abort(422, "Cannot transition from '{$order->order_status}' to '{$newStatus}'.");
        }

        $previousStatus = $order->order_status;

        DB::transaction(function () use ($order, $newStatus, $previousStatus, $changedBy, $note) {
            $order->update(['order_status' => $newStatus]);

            OrderStatusHistory::create([
                'order_id'        => $order->id,
                'previous_status' => $previousStatus,
                'new_status'      => $newStatus,
                'note'            => $note,
                'changed_by'      => $changedBy?->id,
            ]);

            // Handle side effects
            if ($newStatus === 'paid') {
                $this->inventoryService->confirmReservations($order);
            }

            if ($newStatus === 'delivered') {
                $this->loyaltyService->awardOrderPoints($order);
            }

            if ($newStatus === 'cancelled' || $newStatus === 'refunded') {
                $this->inventoryService->releaseReservations($order);
            }
        });

        return $order->fresh();
    }

    public function cancelOrder(Order $order, User $user): void
    {
        $this->updateOrderStatus($order, 'cancelled', $user, 'Cancelled by customer.');
    }
}

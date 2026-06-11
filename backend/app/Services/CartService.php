<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['customer_id' => $user->id]);
    }

    public function addItem(User $user, int $variantId, int $quantity): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $variant = ProductVariant::findOrFail($variantId);

        // Check stock
        $availableStock = $variant->total_stock;
        if ($availableStock < $quantity) {
            abort(422, "Insufficient stock. Only {$availableStock} available.");
        }

        $existingItem = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $quantity;
            if ($newQty > $availableStock) {
                abort(422, "Cannot add more. Only {$availableStock} available.");
            }
            $existingItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id'             => $cart->id,
                'product_variant_id'  => $variantId,
                'quantity'            => $quantity,
                'unit_price'          => $variant->final_price,
            ]);
        }

        return $cart->fresh();
    }

    public function updateItem(User $user, int $itemId, int $quantity): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $item = $cart->items()->findOrFail($itemId);

        $availableStock = $item->productVariant->total_stock;
        if ($quantity > $availableStock) {
            abort(422, "Insufficient stock. Only {$availableStock} available.");
        }

        $item->update(['quantity' => $quantity]);
        return $cart->fresh();
    }

    public function removeItem(User $user, int $itemId): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->where('id', $itemId)->delete();
        return $cart->fresh();
    }

    public function clearCart(User $user): void
    {
        $cart = Cart::where('customer_id', $user->id)->first();
        if ($cart) {
            $cart->items()->delete();
        }
    }

    public function mergeGuestCart(User $user, string $sessionId): Cart
    {
        $guestCart = Cart::where('session_id', $sessionId)->whereNull('customer_id')->first();
        $userCart = $this->getOrCreateCart($user);

        if (!$guestCart) return $userCart;

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $existing = $userCart->items()
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'quantity' => $existing->quantity + $guestItem->quantity,
                    ]);
                } else {
                    CartItem::create([
                        'cart_id'             => $userCart->id,
                        'product_variant_id'  => $guestItem->product_variant_id,
                        'quantity'            => $guestItem->quantity,
                        'unit_price'          => $guestItem->unit_price,
                    ]);
                }
            }
            $guestCart->items()->delete();
            $guestCart->delete();
        });

        return $userCart->fresh();
    }
}

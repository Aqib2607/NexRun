<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\ApiController;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    public function __construct(private CartService $cartService) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $cart->load(['items.productVariant.product.primaryImage', 'items.productVariant.size', 'items.productVariant.color']);
        return $this->success($this->formatCart($cart));
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->cartService->addItem(
            $request->user(),
            $data['product_variant_id'],
            $data['quantity']
        );

        return $this->success($this->formatCart($cart->load(['items.productVariant.product.primaryImage', 'items.productVariant.size', 'items.productVariant.color'])));
    }

    public function updateItem(Request $request, int $item): JsonResponse
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:10']);
        $cart = $this->cartService->updateItem($request->user(), $item, $data['quantity']);
        return $this->success($this->formatCart($cart->load(['items.productVariant.product.primaryImage', 'items.productVariant.size', 'items.productVariant.color'])));
    }

    public function removeItem(Request $request, int $item): JsonResponse
    {
        $cart = $this->cartService->removeItem($request->user(), $item);
        return $this->success($this->formatCart($cart->load(['items.productVariant.product.primaryImage', 'items.productVariant.size', 'items.productVariant.color'])));
    }

    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clearCart($request->user());
        return $this->success(null, 'Cart cleared.');
    }

    public function merge(Request $request): JsonResponse
    {
        $request->validate(['session_id' => 'required|string']);
        $cart = $this->cartService->mergeGuestCart($request->user(), $request->session_id);
        return $this->success($this->formatCart($cart->load(['items.productVariant.product.primaryImage', 'items.productVariant.size', 'items.productVariant.color'])));
    }

    private function formatCart($cart): array
    {
        return [
            'id'         => $cart->id,
            'items'      => $cart->items->map(fn($item) => [
                'id'               => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'product_name'     => $item->productVariant->product->product_name,
                'variant_sku'      => $item->productVariant->variant_sku,
                'size'             => $item->productVariant->size?->size_code,
                'color'            => $item->productVariant->color ? ['name' => $item->productVariant->color->color_name, 'hex' => $item->productVariant->color->hex_code] : null,
                'image'            => $item->productVariant->product->primaryImage?->image_url,
                'quantity'         => $item->quantity,
                'unit_price'       => (float) $item->unit_price,
                'subtotal'         => (float) $item->subtotal,
            ]),
            'item_count' => $cart->item_count,
            'total'      => (float) $cart->total,
        ];
    }
}

<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends ApiController
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $wishlist = Wishlist::firstOrCreate(['customer_id' => $request->user()->id]);
        $items = $wishlist->items()
            ->with(['product.primaryImage', 'product.category', 'product.brand'])
            ->paginate(20);

        $data = $items->through(fn($item) => [
            'id'            => $item->id,
            'product_id'    => $item->product_id,
            'product_name'  => $item->product->product_name,
            'slug'          => $item->product->slug,
            'base_price'    => (float) $item->product->base_price,
            'sale_price'    => $item->product->sale_price ? (float) $item->product->sale_price : null,
            'current_price' => (float) $item->product->current_price,
            'image'         => $item->product->primaryImage?->image_url,
            'in_stock'      => $item->product->activeVariants()->whereHas('inventory', fn($q) => $q->where('quantity_available', '>', 0))->exists(),
            'added_at'      => $item->created_at?->toIso8601String(),
        ]);

        return $this->paginated($data);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = Wishlist::firstOrCreate(['customer_id' => $request->user()->id]);

        $maxItems = config('nexrun.wishlist.max_items', 500);
        if ($wishlist->items()->count() >= $maxItems) {
            return $this->error("Wishlist can have a maximum of {$maxItems} items.", 422);
        }

        WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id'  => $request->product_id,
        ]);

        return $this->created(null, 'Added to wishlist.');
    }

    public function remove(Request $request, int $product): JsonResponse
    {
        $wishlist = Wishlist::where('customer_id', $request->user()->id)->first();
        if ($wishlist) {
            $wishlist->items()->where('product_id', $product)->delete();
        }
        return $this->noContent('Removed from wishlist.');
    }

    public function moveToCart(Request $request, int $product): JsonResponse
    {
        $request->validate(['product_variant_id' => 'required|exists:product_variants,id']);

        $this->cartService->addItem($request->user(), $request->product_variant_id, 1);

        // Remove from wishlist
        $wishlist = Wishlist::where('customer_id', $request->user()->id)->first();
        if ($wishlist) {
            $wishlist->items()->where('product_id', $product)->delete();
        }

        return $this->success(null, 'Moved to cart.');
    }
}

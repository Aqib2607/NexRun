<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    public function forProduct(Product $product, Request $request): JsonResponse
    {
        $reviews = Review::forProduct($product->id)
            ->approved()
            ->with('customer')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        $data = $reviews->through(fn($r) => [
            'id'         => $r->id,
            'rating'     => $r->rating,
            'text'       => $r->review_text,
            'customer'   => $r->customer->full_name,
            'created_at' => $r->created_at?->toIso8601String(),
        ]);

        return $this->paginated($data);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'order_id'    => 'nullable|exists:orders,id',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:2000',
        ]);

        // Check if already reviewed
        $exists = Review::where('product_id', $data['product_id'])
            ->where('customer_id', $request->user()->id)
            ->where('order_id', $data['order_id'] ?? null)
            ->exists();

        if ($exists) {
            return $this->error('You have already reviewed this product.', 422);
        }

        $review = Review::create([
            ...$data,
            'customer_id' => $request->user()->id,
            'status'      => config('nexrun.review.requires_moderation') ? 'pending' : 'approved',
        ]);

        return $this->created([
            'id'     => $review->id,
            'status' => $review->status,
        ], 'Review submitted.');
    }
}

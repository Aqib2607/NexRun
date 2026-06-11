<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::active()
            ->with(['category', 'brand', 'primaryImage', 'activeVariants.size', 'activeVariants.color']);

        if ($request->filled('category_id'))   $query->byCategory($request->category_id);
        if ($request->filled('brand_id'))       $query->byBrand($request->brand_id);
        if ($request->filled('gender'))         $query->byGender($request->gender);
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->inPriceRange($request->min_price, $request->max_price);
        }
        if ($request->filled('on_sale'))        $query->onSale();
        if ($request->filled('in_stock'))       $query->inStock();

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['created_at', 'base_price', 'product_name'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $products = $query->paginate($request->get('per_page', 15));
        return $this->paginated($products->through(fn($p) => new ProductResource($p)));
    }

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'category', 'brand', 'images',
            'activeVariants.size', 'activeVariants.color', 'activeVariants.inventory',
            'approvedReviews.customer',
        ]);

        // Track view
        ProductView::create([
            'customer_id' => auth()->id(),
            'product_id'  => $product->id,
            'session_id'  => session()->getId(),
        ]);

        return $this->success(new ProductResource($product));
    }

    public function featured(Request $request): JsonResponse
    {
        $products = Product::active()->featured()
            ->with(['category', 'brand', 'primaryImage'])
            ->limit($request->get('limit', 12))
            ->get();

        return $this->success(ProductResource::collection($products));
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $products = Product::active()
            ->search($request->q)
            ->with(['category', 'brand', 'primaryImage'])
            ->paginate($request->get('per_page', 15));

        return $this->paginated($products->through(fn($p) => new ProductResource($p)));
    }

    public function related(Product $product, Request $request): JsonResponse
    {
        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id)
                  ->orWhere('brand_id', $product->brand_id);
            })
            ->with(['primaryImage'])
            ->limit($request->get('limit', 8))
            ->inRandomOrder()
            ->get();

        return $this->success(ProductResource::collection($related));
    }
}

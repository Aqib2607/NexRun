<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'primaryImage']);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('category_id')) $query->byCategory($request->category_id);
        if ($request->filled('search'))      $query->where('product_name', 'like', "%{$request->search}%");

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 15))->through(fn($p) => new ProductResource($p)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'product_name'      => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'base_price'        => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'status'            => 'sometimes|in:active,inactive,draft',
            'featured'          => 'sometimes|boolean',
            'gender'            => 'nullable|in:men,women,unisex,kids',
            'images'            => 'sometimes|array',
            'images.*.url'      => 'required_with:images|string',
            'images.*.is_primary' => 'sometimes|boolean',
            'variants'          => 'sometimes|array',
            'variants.*.size_id'  => 'required_with:variants|exists:sizes,id',
            'variants.*.color_id' => 'required_with:variants|exists:colors,id',
            'variants.*.price_adjustment' => 'sometimes|numeric',
        ]);

        $data['sku'] = 'NR-' . strtoupper(Str::random(8));
        $data['slug'] = Str::slug($data['product_name']) . '-' . Str::random(5);

        $product = Product::create($data);

        // Create images
        if (!empty($data['images'])) {
            foreach ($data['images'] as $i => $img) {
                ProductImage::create([
                    'product_id'  => $product->id,
                    'image_url'   => $img['url'],
                    'image_order' => $i,
                    'is_primary'  => $img['is_primary'] ?? ($i === 0),
                ]);
            }
        }

        // Create variants
        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variant) {
                ProductVariant::create([
                    'product_id'       => $product->id,
                    'size_id'          => $variant['size_id'],
                    'color_id'         => $variant['color_id'],
                    'variant_sku'      => $data['sku'] . '-' . strtoupper(Str::random(4)),
                    'price_adjustment' => $variant['price_adjustment'] ?? 0,
                ]);
            }
        }

        return $this->created(new ProductResource($product->load(['category', 'brand', 'images', 'activeVariants.size', 'activeVariants.color'])));
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success(new ProductResource($product->load(['category', 'brand', 'images', 'activeVariants.size', 'activeVariants.color', 'activeVariants.inventory'])));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'category_id'       => 'sometimes|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'product_name'      => 'sometimes|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'base_price'        => 'sometimes|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'status'            => 'sometimes|in:active,inactive,draft',
            'featured'          => 'sometimes|boolean',
            'gender'            => 'nullable|in:men,women,unisex,kids',
        ]);

        if (isset($data['product_name'])) {
            $data['slug'] = Str::slug($data['product_name']) . '-' . Str::random(5);
        }

        $product->update($data);
        return $this->success(new ProductResource($product->fresh()->load(['category', 'brand', 'images', 'activeVariants.size', 'activeVariants.color'])));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->noContent('Product deleted.');
    }
}

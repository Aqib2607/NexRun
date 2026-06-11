<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'sku'                 => $this->sku,
            'name'                => $this->product_name,
            'slug'                => $this->slug,
            'short_description'   => $this->short_description,
            'description'         => $this->when($request->routeIs('*show*') || $request->route('product'), $this->description),
            'base_price'          => (float)$this->base_price,
            'sale_price'          => $this->sale_price ? (float)$this->sale_price : null,
            'current_price'       => (float)$this->current_price,
            'discount_percentage' => $this->discount_percentage,
            'status'              => $this->status,
            'featured'            => $this->featured,
            'gender'              => $this->gender,
            'category'            => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->category_name,
                'slug' => $this->category->slug,
            ]),
            'brand' => $this->whenLoaded('brand', fn() => [
                'id'   => $this->brand?->id,
                'name' => $this->brand?->brand_name,
                'slug' => $this->brand?->slug,
            ]),
            'primary_image' => $this->whenLoaded('primaryImage', fn() => $this->primaryImage?->image_url),
            'images'        => $this->whenLoaded('images', fn() =>
                $this->images->map(fn($img) => [
                    'id'         => $img->id,
                    'url'        => $img->image_url,
                    'order'      => $img->image_order,
                    'is_primary' => $img->is_primary,
                ])
            ),
            'variants' => $this->whenLoaded('activeVariants', fn() =>
                $this->activeVariants->map(fn($v) => [
                    'id'               => $v->id,
                    'sku'              => $v->variant_sku,
                    'size'             => $v->size?->size_code,
                    'color'            => $v->color ? ['name' => $v->color->color_name, 'hex' => $v->color->hex_code] : null,
                    'price_adjustment' => (float)$v->price_adjustment,
                    'final_price'      => (float)$v->final_price,
                    'in_stock'         => $v->total_stock > 0,
                    'stock'            => $v->total_stock,
                ])
            ),
            'reviews' => $this->whenLoaded('approvedReviews', fn() => [
                'average_rating' => round($this->approvedReviews->avg('rating'), 1),
                'count'          => $this->approvedReviews->count(),
                'items'          => $this->approvedReviews->take(5)->map(fn($r) => [
                    'id'           => $r->id,
                    'rating'       => $r->rating,
                    'text'         => $r->review_text,
                    'customer'     => $r->customer->full_name,
                    'created_at'   => $r->created_at?->toIso8601String(),
                ]),
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

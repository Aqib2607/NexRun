<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::with('parent')->ordered()->paginate($request->get('per_page', 30));
        return $this->paginated($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'parent_id'     => 'nullable|exists:categories,id',
            'category_name' => 'required|string|max:100',
            'description'   => 'nullable|string',
            'image'         => 'nullable|string',
            'status'        => 'sometimes|in:active,inactive',
            'sort_order'    => 'sometimes|integer|min:0',
        ]);
        $data['slug'] = Str::slug($data['category_name']);

        return $this->created(Category::create($data));
    }

    public function show(Category $category): JsonResponse
    {
        return $this->success($category->load(['parent', 'children']));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'parent_id'     => 'nullable|exists:categories,id',
            'category_name' => 'sometimes|string|max:100',
            'description'   => 'nullable|string',
            'image'         => 'nullable|string',
            'status'        => 'sometimes|in:active,inactive',
            'sort_order'    => 'sometimes|integer|min:0',
        ]);

        if (isset($data['category_name'])) {
            $data['slug'] = Str::slug($data['category_name']);
        }

        $category->update($data);
        return $this->success($category->fresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return $this->error('Cannot delete category with products.', 422);
        }
        $category->delete();
        return $this->noContent('Category deleted.');
    }
}

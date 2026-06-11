<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController
{
    public function index(): JsonResponse
    {
        $categories = Category::active()->ordered()->get();
        return $this->success($categories);
    }

    public function tree(): JsonResponse
    {
        $tree = Category::active()->roots()->ordered()
            ->with('childrenRecursive')
            ->get();
        return $this->success($tree);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['children', 'parent']);
        return $this->success($category);
    }
}

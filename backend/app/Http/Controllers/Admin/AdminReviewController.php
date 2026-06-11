<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['product', 'customer']);
        if ($request->filled('status')) $query->where('status', $request->status);

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 20)));
    }

    public function moderate(Review $review, Request $request): JsonResponse
    {
        $data = $request->validate(['status' => 'required|in:approved,rejected']);
        $review->update(['status' => $data['status']]);
        return $this->success(['status' => $review->status], 'Review moderated.');
    }
}

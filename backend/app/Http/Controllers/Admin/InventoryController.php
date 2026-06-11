<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends ApiController
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Inventory::with(['productVariant.product', 'productVariant.size', 'productVariant.color', 'warehouse']);

        if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
        if ($request->filled('product_id'))   $query->whereHas('productVariant', fn($q) => $q->where('product_id', $request->product_id));

        return $this->paginated($query->paginate($request->get('per_page', 25)));
    }

    public function update(Inventory $inventory, Request $request): JsonResponse
    {
        $data = $request->validate(['reorder_level' => 'sometimes|integer|min:0']);
        $inventory->update($data);
        return $this->success($inventory->fresh());
    }

    public function lowStock(): JsonResponse
    {
        $items = Inventory::lowStock()
            ->with(['productVariant.product', 'productVariant.size', 'productVariant.color', 'warehouse'])
            ->get();

        return $this->success($items);
    }

    public function adjust(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity'     => 'required|integer',
            'remarks'      => 'required|string|max:500',
        ]);

        $inventory = $this->inventoryService->adjustStock(
            $data['inventory_id'],
            $data['quantity'],
            $data['remarks'],
            $request->user()->id
        );

        return $this->success($inventory->load(['productVariant.product']));
    }
}

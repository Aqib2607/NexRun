<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function reserveStock(int $variantId, int $quantity): void
    {
        $inventory = Inventory::where('product_variant_id', $variantId)->firstOrFail();

        if ($inventory->quantity_available < $quantity) {
            abort(422, "Insufficient stock for variant #{$variantId}.");
        }

        DB::transaction(function () use ($inventory, $quantity) {
            $previousBalance = $inventory->quantity_available;

            $inventory->decrement('quantity_available', $quantity);
            $inventory->increment('quantity_reserved', $quantity);

            InventoryTransaction::create([
                'inventory_id'     => $inventory->id,
                'transaction_type' => 'reservation',
                'quantity'         => -$quantity,
                'previous_balance' => $previousBalance,
                'new_balance'      => $previousBalance - $quantity,
                'remarks'          => 'Stock reserved for order',
                'created_by'       => auth()->id(),
            ]);
        });
    }

    public function confirmReservations(Order $order): void
    {
        foreach ($order->items as $item) {
            $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
            if ($inventory) {
                DB::transaction(function () use ($inventory, $item) {
                    $inventory->decrement('quantity_reserved', $item->quantity);

                    InventoryTransaction::create([
                        'inventory_id'     => $inventory->id,
                        'transaction_type' => 'sale',
                        'quantity'         => -$item->quantity,
                        'previous_balance' => $inventory->quantity_available + $item->quantity,
                        'new_balance'      => $inventory->quantity_available,
                        'remarks'          => "Confirmed for order #{$item->order_id}",
                    ]);
                });
            }
        }
    }

    public function releaseReservations(Order $order): void
    {
        foreach ($order->items as $item) {
            $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
            if ($inventory) {
                DB::transaction(function () use ($inventory, $item) {
                    $previousBalance = $inventory->quantity_available;

                    $inventory->increment('quantity_available', $item->quantity);
                    $inventory->decrement('quantity_reserved', $item->quantity);

                    InventoryTransaction::create([
                        'inventory_id'     => $inventory->id,
                        'transaction_type' => 'release',
                        'quantity'         => $item->quantity,
                        'previous_balance' => $previousBalance,
                        'new_balance'      => $previousBalance + $item->quantity,
                        'remarks'          => "Released from cancelled order #{$item->order_id}",
                    ]);
                });
            }
        }
    }

    public function adjustStock(int $inventoryId, int $quantity, string $remarks, ?int $userId = null): Inventory
    {
        $inventory = Inventory::findOrFail($inventoryId);

        return DB::transaction(function () use ($inventory, $quantity, $remarks, $userId) {
            $previousBalance = $inventory->quantity_available;
            $newBalance = $previousBalance + $quantity;

            if ($newBalance < 0) {
                abort(422, 'Adjustment would result in negative stock.');
            }

            $inventory->update(['quantity_available' => $newBalance]);

            InventoryTransaction::create([
                'inventory_id'     => $inventory->id,
                'transaction_type' => 'adjustment',
                'quantity'         => $quantity,
                'previous_balance' => $previousBalance,
                'new_balance'      => $newBalance,
                'remarks'          => $remarks,
                'created_by'       => $userId ?? auth()->id(),
            ]);

            return $inventory->fresh();
        });
    }
}

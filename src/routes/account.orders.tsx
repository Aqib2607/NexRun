import { createFileRoute } from "@tanstack/react-router";
import { Package } from "lucide-react";

export const Route = createFileRoute("/account/orders")({
  component: () => (
    <div>
      <h1 className="font-display text-3xl font-black mb-6">Orders</h1>
      <div className="bg-surface rounded-xl p-12 text-center">
        <Package className="h-10 w-10 mx-auto text-muted-foreground" />
        <p className="mt-3 font-semibold">No orders yet</p>
        <p className="text-sm text-muted-foreground mt-1">Your future orders will appear here.</p>
      </div>
    </div>
  ),
});

import { createFileRoute, Link } from "@tanstack/react-router";
import { useCart } from "@/store/cart";
import { money } from "@/lib/format";
import { Minus, Plus, Trash2 } from "lucide-react";

export const Route = createFileRoute("/cart")({
  head: () => ({ meta: [{ title: "Bag — NexRun" }] }),
  component: CartPage,
});

function CartPage() {
  const { items, updateQty, remove, subtotal } = useCart();
  const shipping = items.length ? (subtotal() >= 100 ? 0 : 10) : 0;
  const tax = subtotal() * 0.08;
  const total = subtotal() + shipping + tax;

  if (items.length === 0) {
    return (
      <div className="container-nx py-24 text-center">
        <h1 className="font-display text-4xl font-black">Your bag is empty</h1>
        <p className="text-muted-foreground mt-3">Looks like you haven't added anything yet.</p>
        <Link to="/shop" className="mt-6 inline-block bg-primary text-primary-foreground px-7 py-3.5 rounded-full font-semibold">
          Shop now
        </Link>
      </div>
    );
  }

  return (
    <div className="container-nx py-10 md:py-16">
      <h1 className="font-display text-4xl md:text-5xl font-black mb-10">Your Bag</h1>
      <div className="grid lg:grid-cols-[1fr_380px] gap-10">
        <div className="space-y-6">
          {items.map((i) => (
            <div key={`${i.productId}-${i.color}-${i.size}`} className="flex gap-4 md:gap-6 border-b border-border pb-6">
              <img src={i.image} alt={i.name} className="h-32 w-32 md:h-40 md:w-40 object-cover rounded-lg bg-surface" />
              <div className="flex-1 min-w-0">
                <div className="flex justify-between gap-3">
                  <div className="min-w-0">
                    <h3 className="font-semibold truncate">{i.name}</h3>
                    <p className="text-sm text-muted-foreground mt-1">{i.color} · Size {i.size}</p>
                  </div>
                  <p className="font-semibold">{money(i.price * i.qty)}</p>
                </div>
                <div className="mt-4 flex items-center justify-between">
                  <div className="flex items-center border border-border rounded-full">
                    <button onClick={() => updateQty(i.productId, i.color, i.size, i.qty - 1)} className="p-2"><Minus className="h-3.5 w-3.5" /></button>
                    <span className="px-4 text-sm tabular-nums">{i.qty}</span>
                    <button onClick={() => updateQty(i.productId, i.color, i.size, i.qty + 1)} className="p-2"><Plus className="h-3.5 w-3.5" /></button>
                  </div>
                  <button onClick={() => remove(i.productId, i.color, i.size)} className="text-muted-foreground hover:text-destructive" aria-label="Remove">
                    <Trash2 className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>

        <aside className="bg-surface rounded-xl p-6 h-fit lg:sticky lg:top-24">
          <h2 className="font-display text-xl font-bold mb-5">Order Summary</h2>
          <div className="space-y-3 text-sm">
            <Row label="Subtotal" value={money(subtotal())} />
            <Row label="Shipping" value={shipping === 0 ? "Free" : money(shipping)} />
            <Row label="Tax" value={money(tax)} />
            <div className="border-t border-border pt-3 mt-3">
              <Row label="Total" value={money(total)} bold />
            </div>
          </div>
          <Link to="/checkout" className="mt-6 block text-center bg-primary text-primary-foreground py-3.5 rounded-full font-semibold hover:opacity-90">
            Proceed to Checkout
          </Link>
          <Link to="/shop" className="mt-3 block text-center text-sm underline underline-offset-4">Continue shopping</Link>
        </aside>
      </div>
    </div>
  );
}

function Row({ label, value, bold }: { label: string; value: string; bold?: boolean }) {
  return (
    <div className={`flex justify-between ${bold ? "font-bold text-base" : ""}`}>
      <span className={bold ? "" : "text-muted-foreground"}>{label}</span>
      <span>{value}</span>
    </div>
  );
}

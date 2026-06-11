import { useCart } from "@/store/cart";
import { Link } from "@tanstack/react-router";
import { X, Minus, Plus, Trash2 } from "lucide-react";
import { AnimatePresence, motion } from "framer-motion";
import { money } from "@/lib/format";

export function CartDrawer() {
  const { isOpen, close, items, updateQty, remove, subtotal } = useCart();

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={close}
            className="fixed inset-0 z-50 bg-black/50"
          />
          <motion.aside
            initial={{ x: "100%" }}
            animate={{ x: 0 }}
            exit={{ x: "100%" }}
            transition={{ type: "tween", duration: 0.3 }}
            className="fixed right-0 top-0 z-50 h-dvh w-full sm:w-[420px] bg-background flex flex-col shadow-2xl"
          >
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-display text-lg font-bold">Your Bag ({items.length})</h2>
              <button onClick={close} aria-label="Close cart" className="p-2 hover:bg-muted rounded-full">
                <X className="h-5 w-5" />
              </button>
            </div>

            {items.length === 0 ? (
              <div className="flex-1 grid place-items-center text-center p-8">
                <div>
                  <p className="text-muted-foreground">Your bag is empty.</p>
                  <Link to="/shop" onClick={close} className="inline-block mt-4 bg-primary text-primary-foreground px-6 py-3 rounded-md text-sm font-semibold">
                    Start shopping
                  </Link>
                </div>
              </div>
            ) : (
              <>
                <div className="flex-1 overflow-y-auto p-5 space-y-5">
                  {items.map((i) => (
                    <div key={`${i.productId}-${i.color}-${i.size}`} className="flex gap-4">
                      <img src={i.image} alt={i.name} className="h-24 w-24 rounded-md object-cover bg-muted" />
                      <div className="flex-1 min-w-0">
                        <div className="flex justify-between gap-2">
                          <p className="font-medium truncate">{i.name}</p>
                          <p className="font-semibold">{money(i.price * i.qty)}</p>
                        </div>
                        <p className="text-xs text-muted-foreground mt-0.5">{i.color} · Size {i.size}</p>
                        <div className="mt-3 flex items-center justify-between">
                          <div className="flex items-center border border-border rounded-full">
                            <button onClick={() => updateQty(i.productId, i.color, i.size, i.qty - 1)} className="p-1.5"><Minus className="h-3.5 w-3.5" /></button>
                            <span className="px-3 text-sm tabular-nums">{i.qty}</span>
                            <button onClick={() => updateQty(i.productId, i.color, i.size, i.qty + 1)} className="p-1.5"><Plus className="h-3.5 w-3.5" /></button>
                          </div>
                          <button onClick={() => remove(i.productId, i.color, i.size)} aria-label="Remove" className="text-muted-foreground hover:text-destructive">
                            <Trash2 className="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="border-t border-border p-5 space-y-3">
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Subtotal</span>
                    <span className="font-semibold">{money(subtotal())}</span>
                  </div>
                  <p className="text-xs text-muted-foreground">Shipping and taxes calculated at checkout.</p>
                  <Link to="/checkout" onClick={close} className="block text-center bg-primary text-primary-foreground py-3.5 rounded-md font-semibold hover:opacity-90 transition">
                    Checkout
                  </Link>
                  <Link to="/cart" onClick={close} className="block text-center text-sm underline underline-offset-4">
                    View bag
                  </Link>
                </div>
              </>
            )}
          </motion.aside>
        </>
      )}
    </AnimatePresence>
  );
}

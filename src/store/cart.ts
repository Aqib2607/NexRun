import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { CartItem } from "@/types";

type CartState = {
  items: CartItem[];
  isOpen: boolean;
  add: (item: CartItem) => void;
  remove: (productId: string, color: string, size: string) => void;
  updateQty: (productId: string, color: string, size: string, qty: number) => void;
  clear: () => void;
  open: () => void;
  close: () => void;
  toggle: () => void;
  subtotal: () => number;
  count: () => number;
};

const key = (i: { productId: string; color: string; size: string }) =>
  `${i.productId}__${i.color}__${i.size}`;

export const useCart = create<CartState>()(
  persist(
    (set, get) => ({
      items: [],
      isOpen: false,
      add: (item) =>
        set((s) => {
          const idx = s.items.findIndex((i) => key(i) === key(item));
          if (idx >= 0) {
            const items = [...s.items];
            items[idx] = { ...items[idx], qty: items[idx].qty + item.qty };
            return { items, isOpen: true };
          }
          return { items: [...s.items, item], isOpen: true };
        }),
      remove: (productId, color, size) =>
        set((s) => ({
          items: s.items.filter((i) => !(i.productId === productId && i.color === color && i.size === size)),
        })),
      updateQty: (productId, color, size, qty) =>
        set((s) => ({
          items: s.items.map((i) =>
            i.productId === productId && i.color === color && i.size === size
              ? { ...i, qty: Math.max(1, qty) }
              : i,
          ),
        })),
      clear: () => set({ items: [] }),
      open: () => set({ isOpen: true }),
      close: () => set({ isOpen: false }),
      toggle: () => set((s) => ({ isOpen: !s.isOpen })),
      subtotal: () => get().items.reduce((sum, i) => sum + i.price * i.qty, 0),
      count: () => get().items.reduce((sum, i) => sum + i.qty, 0),
    }),
    { name: "nexrun-cart" },
  ),
);

import { Link } from "@tanstack/react-router";
import { Heart, Search, ShoppingBag, User, Menu, X } from "lucide-react";
import { useState } from "react";
import { useCart } from "@/store/cart";
import { useWishlist } from "@/store/wishlist";
import { motion, AnimatePresence } from "framer-motion";

const NAV = [
  { to: "/category/running", label: "Running" },
  { to: "/category/training", label: "Training" },
  { to: "/category/lifestyle", label: "Lifestyle" },
  { to: "/category/basketball", label: "Basketball" },
  { to: "/shop", label: "Shop All" },
];

export function Navbar() {
  const open = useCart((s) => s.open);
  const count = useCart((s) => s.items.reduce((a, b) => a + b.qty, 0));
  const wishCount = useWishlist((s) => s.ids.length);
  const [mobile, setMobile] = useState(false);

  return (
    <header className="sticky top-0 z-40 border-b border-border bg-background/85 backdrop-blur-lg">
      <div className="container-nx flex h-16 items-center gap-6">
        <button
          className="lg:hidden -ml-1 p-2"
          aria-label="Open menu"
          onClick={() => setMobile(true)}
        >
          <Menu className="h-5 w-5" />
        </button>

        <Link to="/" className="font-display text-2xl font-black tracking-tight">
          NEX<span className="text-accent">RUN</span>
        </Link>

        <nav className="hidden lg:flex items-center gap-7 ml-4">
          {NAV.map((n) => (
            <Link
              key={n.to}
              to={n.to}
              className="text-sm font-medium text-foreground/80 hover:text-foreground transition-colors"
              activeProps={{ className: "text-foreground" }}
            >
              {n.label}
            </Link>
          ))}
        </nav>

        <div className="ml-auto flex items-center gap-1">
          <Link to="/search" aria-label="Search" className="p-2 hover:bg-muted rounded-full transition">
            <Search className="h-5 w-5" />
          </Link>
          <Link to="/account" aria-label="Account" className="p-2 hover:bg-muted rounded-full transition hidden sm:inline-flex">
            <User className="h-5 w-5" />
          </Link>
          <Link to="/wishlist" aria-label="Wishlist" className="relative p-2 hover:bg-muted rounded-full transition">
            <Heart className="h-5 w-5" />
            {wishCount > 0 && (
              <span className="absolute top-1 right-1 h-4 min-w-4 px-1 rounded-full bg-accent text-accent-foreground text-[10px] font-bold grid place-items-center">
                {wishCount}
              </span>
            )}
          </Link>
          <button
            onClick={open}
            aria-label="Cart"
            className="relative p-2 hover:bg-muted rounded-full transition"
          >
            <ShoppingBag className="h-5 w-5" />
            {count > 0 && (
              <span className="absolute top-1 right-1 h-4 min-w-4 px-1 rounded-full bg-accent text-accent-foreground text-[10px] font-bold grid place-items-center">
                {count}
              </span>
            )}
          </button>
        </div>
      </div>

      <AnimatePresence>
        {mobile && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 bg-background lg:hidden"
          >
            <div className="flex h-16 items-center justify-between px-5 border-b border-border">
              <span className="font-display text-xl font-black">NEX<span className="text-accent">RUN</span></span>
              <button aria-label="Close menu" onClick={() => setMobile(false)} className="p-2">
                <X className="h-5 w-5" />
              </button>
            </div>
            <nav className="px-5 py-8 flex flex-col gap-1">
              {NAV.map((n) => (
                <Link
                  key={n.to}
                  to={n.to}
                  onClick={() => setMobile(false)}
                  className="py-4 text-2xl font-display font-bold border-b border-border"
                >
                  {n.label}
                </Link>
              ))}
              <Link onClick={() => setMobile(false)} to="/account" className="py-4 text-base">Account</Link>
              <Link onClick={() => setMobile(false)} to="/wishlist" className="py-4 text-base">Wishlist</Link>
            </nav>
          </motion.div>
        )}
      </AnimatePresence>
    </header>
  );
}

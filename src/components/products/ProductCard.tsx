import { Link } from "@tanstack/react-router";
import { Heart } from "lucide-react";
import type { Product } from "@/types";
import { useWishlist } from "@/store/wishlist";
import { money } from "@/lib/format";
import { motion } from "framer-motion";

export function ProductCard({ product, index = 0 }: { product: Product; index?: number }) {
  const { has, toggle } = useWishlist();
  const liked = has(product.id);

  return (
    <motion.div
      initial={{ opacity: 0, y: 16 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-50px" }}
      transition={{ duration: 0.4, delay: Math.min(index * 0.04, 0.3) }}
      className="group"
    >
      <Link
        to="/product/$slug"
        params={{ slug: product.slug }}
        className="block"
      >
        <div className="relative aspect-square overflow-hidden rounded-lg bg-surface">
          <img
            src={product.images[0]}
            alt={product.name}
            loading="lazy"
            className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
          {product.badge && (
            <span className="absolute top-3 left-3 bg-background text-foreground text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
              {product.badge}
            </span>
          )}
          <button
            onClick={(e) => { e.preventDefault(); toggle(product.id); }}
            aria-label={liked ? "Remove from wishlist" : "Add to wishlist"}
            className="absolute top-3 right-3 h-9 w-9 grid place-items-center rounded-full bg-background/90 backdrop-blur hover:bg-background transition"
          >
            <Heart className={`h-4 w-4 ${liked ? "fill-accent text-accent" : ""}`} />
          </button>
        </div>
        <div className="mt-3 space-y-1">
          <p className="text-xs uppercase tracking-wider text-muted-foreground">{product.category}</p>
          <h3 className="font-medium text-sm">{product.name}</h3>
          <div className="flex items-baseline gap-2">
            <span className="font-semibold">{money(product.price)}</span>
            {product.compareAt && (
              <span className="text-xs text-muted-foreground line-through">{money(product.compareAt)}</span>
            )}
          </div>
        </div>
      </Link>
    </motion.div>
  );
}

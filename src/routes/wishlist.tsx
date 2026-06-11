import { createFileRoute, Link } from "@tanstack/react-router";
import { useWishlist } from "@/store/wishlist";
import { products } from "@/lib/mock-data";
import { ProductCard } from "@/components/products/ProductCard";

export const Route = createFileRoute("/wishlist")({
  head: () => ({ meta: [{ title: "Wishlist — NexRun" }] }),
  component: WishlistPage,
});

function WishlistPage() {
  const ids = useWishlist((s) => s.ids);
  const items = products.filter((p) => ids.includes(p.id));

  return (
    <div className="container-nx py-10 md:py-16">
      <h1 className="font-display text-4xl md:text-5xl font-black mb-2">Wishlist</h1>
      <p className="text-muted-foreground mb-10">{items.length} saved items</p>
      {items.length === 0 ? (
        <div className="text-center py-16">
          <p className="text-muted-foreground">You haven't saved anything yet.</p>
          <Link to="/shop" className="mt-5 inline-block bg-primary text-primary-foreground px-6 py-3 rounded-full font-semibold">
            Browse products
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
          {items.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
        </div>
      )}
    </div>
  );
}

import { createFileRoute, Link, notFound } from "@tanstack/react-router";
import { getProduct, products } from "@/lib/mock-data";
import { useState } from "react";
import { Heart, Truck, RotateCcw, Shield, Star } from "lucide-react";
import { useCart } from "@/store/cart";
import { useWishlist } from "@/store/wishlist";
import { ProductCard } from "@/components/products/ProductCard";
import { money } from "@/lib/format";
import { motion } from "framer-motion";
import { toast } from "sonner";

export const Route = createFileRoute("/product/$slug")({
  loader: ({ params }) => {
    const product = getProduct(params.slug);
    if (!product) throw notFound();
    return { product };
  },
  head: ({ loaderData }) => ({
    meta: [
      { title: `${loaderData?.product.name} — NexRun` },
      { name: "description", content: loaderData?.product.description.slice(0, 155) },
      { property: "og:image", content: loaderData?.product.images[0] },
    ],
  }),
  notFoundComponent: () => (
    <div className="container-nx py-24 text-center">
      <h1 className="font-display text-3xl font-bold">Product not found</h1>
      <Link to="/shop" className="mt-4 inline-block underline">Back to shop</Link>
    </div>
  ),
  component: PDP,
});

function PDP() {
  const { product } = Route.useLoaderData();
  const [color, setColor] = useState(product.colors[0]);
  const [size, setSize] = useState<string | null>(null);
  const [activeImg, setActiveImg] = useState(0);
  const add = useCart((s) => s.add);
  const { has, toggle } = useWishlist();
  const liked = has(product.id);

  const related = products.filter((p) => p.category === product.category && p.id !== product.id).slice(0, 4);

  const handleAdd = () => {
    if (!size) {
      toast.error("Please select a size");
      return;
    }
    add({
      productId: product.id,
      slug: product.slug,
      name: product.name,
      image: product.images[0],
      price: product.price,
      color,
      size,
      qty: 1,
    });
    toast.success("Added to bag");
  };

  return (
    <div className="container-nx py-8 md:py-12">
      <nav className="text-xs text-muted-foreground mb-6">
        <Link to="/shop">Shop</Link> / <span className="capitalize">{product.category}</span> / <span className="text-foreground">{product.name}</span>
      </nav>

      <div className="grid lg:grid-cols-[1.2fr_1fr] gap-8 lg:gap-16">
        {/* Gallery */}
        <div className="grid grid-cols-[80px_1fr] gap-3">
          <div className="hidden md:flex flex-col gap-2">
            {product.images.map((src, i) => (
              <button
                key={i}
                onClick={() => setActiveImg(i)}
                className={`aspect-square rounded-md overflow-hidden bg-surface border-2 transition ${activeImg === i ? "border-foreground" : "border-transparent"}`}
              >
                <img src={src} alt="" className="h-full w-full object-cover" />
              </button>
            ))}
          </div>
          <motion.div
            key={activeImg}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            className="col-span-2 md:col-span-1 aspect-square bg-surface rounded-xl overflow-hidden"
          >
            <img src={product.images[activeImg]} alt={product.name} className="h-full w-full object-cover" />
          </motion.div>
          <div className="md:hidden col-span-2 flex gap-2 overflow-x-auto">
            {product.images.map((src, i) => (
              <button key={i} onClick={() => setActiveImg(i)} className={`shrink-0 h-16 w-16 rounded-md overflow-hidden border-2 ${activeImg === i ? "border-foreground" : "border-transparent"}`}>
                <img src={src} alt="" className="h-full w-full object-cover" />
              </button>
            ))}
          </div>
        </div>

        {/* Info */}
        <div>
          {product.badge && <span className="eyebrow text-accent">{product.badge}</span>}
          <h1 className="font-display text-3xl md:text-4xl font-black mt-2">{product.name}</h1>
          <p className="text-muted-foreground mt-1 capitalize">{product.category}</p>

          <div className="flex items-center gap-3 mt-4">
            <div className="flex">
              {Array.from({ length: 5 }).map((_, i) => (
                <Star key={i} className={`h-4 w-4 ${i < Math.round(product.rating) ? "fill-foreground text-foreground" : "text-muted-foreground"}`} />
              ))}
            </div>
            <span className="text-sm text-muted-foreground">{product.rating.toFixed(1)} · {product.reviewCount} reviews</span>
          </div>

          <div className="flex items-baseline gap-3 mt-6">
            <span className="text-3xl font-bold">{money(product.price)}</span>
            {product.compareAt && <span className="text-muted-foreground line-through">{money(product.compareAt)}</span>}
          </div>

          <div className="mt-8">
            <div className="flex justify-between text-sm mb-3">
              <span className="font-semibold">Color</span>
              <span className="text-muted-foreground">{color}</span>
            </div>
            <div className="flex gap-2">
              {product.colors.map((c) => (
                <button
                  key={c}
                  aria-label={c}
                  onClick={() => setColor(c)}
                  className={`px-4 py-2 text-sm rounded-md border transition ${color === c ? "border-foreground bg-foreground text-background" : "border-border hover:border-foreground"}`}
                >
                  {c}
                </button>
              ))}
            </div>
          </div>

          <div className="mt-6">
            <div className="flex justify-between text-sm mb-3">
              <span className="font-semibold">Size</span>
              <button className="text-muted-foreground underline">Size guide</button>
            </div>
            <div className="grid grid-cols-5 gap-2">
              {product.sizes.map((s) => (
                <button
                  key={s}
                  onClick={() => setSize(s)}
                  className={`py-3 text-sm rounded-md border transition ${size === s ? "border-foreground bg-foreground text-background" : "border-border hover:border-foreground"}`}
                >
                  {s}
                </button>
              ))}
            </div>
          </div>

          <div className="mt-8 flex gap-3">
            <button
              onClick={handleAdd}
              className="flex-1 bg-primary text-primary-foreground py-4 rounded-full font-semibold hover:opacity-90 transition"
            >
              Add to bag
            </button>
            <button
              onClick={() => toggle(product.id)}
              aria-label="Wishlist"
              className="h-14 w-14 grid place-items-center border border-border rounded-full hover:border-foreground transition"
            >
              <Heart className={`h-5 w-5 ${liked ? "fill-accent text-accent" : ""}`} />
            </button>
          </div>

          <p className="mt-8 text-foreground/80 leading-relaxed">{product.description}</p>

          <div className="mt-8 grid grid-cols-3 gap-3 text-xs">
            {[
              [Truck, "Free shipping", "Over $100"],
              [RotateCcw, "30-day returns", "Easy & free"],
              [Shield, "2-year warranty", "Crafted to last"],
            ].map(([Icon, t, s], i) => {
              const I = Icon as typeof Truck;
              return (
                <div key={i} className="border border-border rounded-lg p-3">
                  <I className="h-4 w-4 mb-2" />
                  <p className="font-semibold">{t as string}</p>
                  <p className="text-muted-foreground">{s as string}</p>
                </div>
              );
            })}
          </div>
        </div>
      </div>

      {related.length > 0 && (
        <section className="mt-20">
          <h2 className="font-display text-2xl md:text-3xl font-black mb-8">You may also like</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-10">
            {related.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
          </div>
        </section>
      )}
    </div>
  );
}

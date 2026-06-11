import { createFileRoute, Link } from "@tanstack/react-router";
import { motion } from "framer-motion";
import { ArrowRight } from "lucide-react";
import heroImg from "@/assets/hero-runner.jpg";
import storyImg from "@/assets/brand-story.jpg";
import { categories, newArrivals, bestSellers, products } from "@/lib/mock-data";
import { ProductCard } from "@/components/products/ProductCard";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "NexRun — Engineered for Athletes" },
      { name: "description", content: "Shop premium running, training and lifestyle gear from NexRun." },
    ],
  }),
  component: Home,
});

function Home() {
  const featured = newArrivals.length ? newArrivals : products.slice(0, 8);
  const best = bestSellers.length ? bestSellers : products.slice(8, 16);

  return (
    <>
      {/* Hero */}
      <section className="relative h-[100svh] min-h-[600px] w-full overflow-hidden bg-ink text-white">
        <img
          src={heroImg}
          alt="Sprinter on a track"
          className="absolute inset-0 h-full w-full object-cover opacity-80"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent" />
        <div className="relative container-nx h-full flex items-end pb-16 md:pb-24 lg:items-center">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7 }}
            className="max-w-xl"
          >
            <span className="eyebrow text-accent">New Season · SS26</span>
            <h1 className="mt-4 font-display text-5xl sm:text-6xl lg:text-7xl font-black leading-[0.95] text-balance">
              Move Without Limits.
            </h1>
            <p className="mt-5 text-lg text-white/80 max-w-md">
              Engineered to outlast every mile, every rep, every PR. Discover the new Velocity Pro collection.
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Link
                to="/shop"
                className="inline-flex items-center gap-2 bg-white text-ink px-7 py-4 rounded-full font-semibold hover:bg-white/90 transition"
              >
                Shop Collection <ArrowRight className="h-4 w-4" />
              </Link>
              <Link
                to="/category/running"
                className="inline-flex items-center gap-2 border border-white/30 text-white px-7 py-4 rounded-full font-semibold hover:bg-white/10 transition"
              >
                Explore Running
              </Link>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Categories */}
      <section className="container-nx mt-20 md:mt-28">
        <div className="flex items-end justify-between mb-8">
          <div>
            <span className="eyebrow text-muted-foreground">Shop by category</span>
            <h2 className="font-display text-3xl md:text-4xl font-black mt-2">Pick Your Arena</h2>
          </div>
          <Link to="/shop" className="hidden sm:inline-flex items-center gap-1 text-sm font-semibold underline underline-offset-4">
            View all <ArrowRight className="h-4 w-4" />
          </Link>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-5">
          {categories.slice(0, 3).map((c, i) => (
            <Link
              key={c.slug}
              to="/category/$slug"
              params={{ slug: c.slug }}
              className={`group relative overflow-hidden rounded-xl bg-ink ${i === 0 ? "col-span-2 md:col-span-1 aspect-[4/5]" : "aspect-[4/5]"}`}
            >
              <img src={c.image} alt={c.name} loading="lazy" className="h-full w-full object-cover opacity-85 transition-transform duration-500 group-hover:scale-105" />
              <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
              <div className="absolute bottom-0 left-0 right-0 p-6 text-white">
                <p className="eyebrow text-accent">{c.tagline}</p>
                <h3 className="font-display text-2xl md:text-3xl font-black mt-1">{c.name}</h3>
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* New Arrivals */}
      <section className="container-nx mt-20 md:mt-28">
        <div className="flex items-end justify-between mb-8">
          <div>
            <span className="eyebrow text-accent">Just dropped</span>
            <h2 className="font-display text-3xl md:text-4xl font-black mt-2">New Arrivals</h2>
          </div>
          <Link to="/shop" className="text-sm font-semibold underline underline-offset-4">See all</Link>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
          {featured.slice(0, 8).map((p, i) => (
            <ProductCard key={p.id} product={p} index={i} />
          ))}
        </div>
      </section>

      {/* Brand story */}
      <section className="container-nx mt-20 md:mt-28">
        <div className="grid md:grid-cols-2 gap-8 md:gap-12 items-center bg-ink text-white rounded-2xl overflow-hidden">
          <div className="aspect-[4/3] md:aspect-auto md:h-full">
            <img src={storyImg} alt="Athletes training" loading="lazy" className="h-full w-full object-cover" />
          </div>
          <div className="p-8 md:p-12 lg:p-16">
            <span className="eyebrow text-accent">Our story</span>
            <h2 className="mt-3 font-display text-3xl md:text-4xl font-black leading-tight">
              Built by athletes, for athletes who refuse easy.
            </h2>
            <p className="mt-4 text-white/70">
              Every NexRun product is field-tested by the people who depend on it. From elite marathoners to weekend warriors,
              we obsess over the details that matter when the work gets hard.
            </p>
            <Link to="/about" className="mt-6 inline-flex items-center gap-2 text-accent font-semibold">
              Discover NexRun <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </section>

      {/* Best sellers */}
      <section className="container-nx mt-20 md:mt-28">
        <div className="flex items-end justify-between mb-8">
          <div>
            <span className="eyebrow text-muted-foreground">Loved by athletes</span>
            <h2 className="font-display text-3xl md:text-4xl font-black mt-2">Best Sellers</h2>
          </div>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
          {best.slice(0, 4).map((p, i) => (
            <ProductCard key={p.id} product={p} index={i} />
          ))}
        </div>
      </section>

      {/* Newsletter */}
      <section className="container-nx mt-20 md:mt-28">
        <div className="rounded-2xl bg-surface p-10 md:p-16 text-center">
          <span className="eyebrow text-accent">Newsletter</span>
          <h2 className="mt-3 font-display text-3xl md:text-4xl font-black">Train with us. Save first.</h2>
          <p className="mt-3 text-muted-foreground max-w-lg mx-auto">
            Get 10% off your first order, plus early access to drops and athlete stories.
          </p>
          <form className="mt-6 flex flex-col sm:flex-row gap-2 max-w-md mx-auto" onSubmit={(e) => e.preventDefault()}>
            <input
              type="email"
              placeholder="you@example.com"
              className="flex-1 px-4 py-3 rounded-md border border-border bg-background"
            />
            <button className="bg-primary text-primary-foreground px-6 py-3 rounded-md font-semibold hover:opacity-90">
              Subscribe
            </button>
          </form>
        </div>
      </section>
    </>
  );
}

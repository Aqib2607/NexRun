import { createFileRoute, Link } from "@tanstack/react-router";
import { useState, useMemo } from "react";
import { products } from "@/lib/mock-data";
import { ProductCard } from "@/components/products/ProductCard";
import { Search } from "lucide-react";

export const Route = createFileRoute("/search")({
  head: () => ({ meta: [{ title: "Search — NexRun" }] }),
  component: SearchPage,
});

function SearchPage() {
  const [q, setQ] = useState("");
  const results = useMemo(() => {
    if (!q) return [];
    const t = q.toLowerCase();
    return products.filter((p) => p.name.toLowerCase().includes(t) || p.category.includes(t));
  }, [q]);
  return (
    <div className="container-nx py-10 md:py-16">
      <h1 className="font-display text-3xl md:text-4xl font-black mb-6">Search</h1>
      <div className="relative max-w-xl">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <input
          autoFocus
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="Search products, categories…"
          className="w-full pl-11 pr-4 py-3.5 rounded-full border border-border bg-background"
        />
      </div>
      <div className="mt-10">
        {!q && <p className="text-muted-foreground">Start typing to search.</p>}
        {q && results.length === 0 && (
          <div>
            <p className="text-muted-foreground">No results for "{q}".</p>
            <Link to="/shop" className="text-sm underline mt-2 inline-block">Browse all</Link>
          </div>
        )}
        {results.length > 0 && (
          <>
            <p className="text-sm text-muted-foreground mb-6">{results.length} results</p>
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
              {results.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
            </div>
          </>
        )}
      </div>
    </div>
  );
}

import { createFileRoute } from "@tanstack/react-router";
import { products, COLORS, SIZES, categories } from "@/lib/mock-data";
import { ProductCard } from "@/components/products/ProductCard";
import { useMemo, useState } from "react";
import { SlidersHorizontal, X } from "lucide-react";

export const Route = createFileRoute("/shop")({
  head: () => ({
    meta: [
      { title: "Shop All — NexRun" },
      { name: "description", content: "Browse the full NexRun collection of performance footwear and sportswear." },
    ],
  }),
  component: Shop,
});

const SORTS = [
  { id: "new", label: "Newest" },
  { id: "popular", label: "Popular" },
  { id: "price-asc", label: "Price: Low to High" },
  { id: "price-desc", label: "Price: High to Low" },
] as const;

function Shop() {
  const [sort, setSort] = useState<(typeof SORTS)[number]["id"]>("popular");
  const [cats, setCats] = useState<string[]>([]);
  const [colorSel, setColorSel] = useState<string[]>([]);
  const [sizeSel, setSizeSel] = useState<string[]>([]);
  const [price, setPrice] = useState<[number, number]>([0, 300]);
  const [open, setOpen] = useState(false);

  const filtered = useMemo(() => {
    let r = products.slice();
    if (cats.length) r = r.filter((p) => cats.includes(p.category));
    if (colorSel.length) r = r.filter((p) => p.colors.some((c) => colorSel.includes(c)));
    if (sizeSel.length) r = r.filter((p) => p.sizes.some((s) => sizeSel.includes(s)));
    r = r.filter((p) => p.price >= price[0] && p.price <= price[1]);
    switch (sort) {
      case "new": r.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0)); break;
      case "popular": r.sort((a, b) => b.reviewCount - a.reviewCount); break;
      case "price-asc": r.sort((a, b) => a.price - b.price); break;
      case "price-desc": r.sort((a, b) => b.price - a.price); break;
    }
    return r;
  }, [sort, cats, colorSel, sizeSel, price]);

  const toggle = (arr: string[], set: (v: string[]) => void, v: string) =>
    set(arr.includes(v) ? arr.filter((x) => x !== v) : [...arr, v]);

  const Filters = (
    <div className="space-y-8">
      <FilterGroup title="Category">
        {categories.map((c) => (
          <Check key={c.slug} label={c.name} checked={cats.includes(c.slug)} onChange={() => toggle(cats, setCats, c.slug)} />
        ))}
      </FilterGroup>
      <FilterGroup title="Color">
        <div className="flex flex-wrap gap-2">
          {COLORS.map((c) => (
            <button
              key={c}
              onClick={() => toggle(colorSel, setColorSel, c)}
              className={`px-3 py-1.5 text-xs rounded-full border transition ${colorSel.includes(c) ? "bg-primary text-primary-foreground border-primary" : "border-border hover:border-foreground"}`}
            >
              {c}
            </button>
          ))}
        </div>
      </FilterGroup>
      <FilterGroup title="Size">
        <div className="grid grid-cols-5 gap-2">
          {SIZES.map((s) => (
            <button
              key={s}
              onClick={() => toggle(sizeSel, setSizeSel, s)}
              className={`py-2 text-xs rounded-md border transition ${sizeSel.includes(s) ? "bg-primary text-primary-foreground border-primary" : "border-border hover:border-foreground"}`}
            >
              {s}
            </button>
          ))}
        </div>
      </FilterGroup>
      <FilterGroup title={`Price · $${price[0]} – $${price[1]}`}>
        <input
          type="range"
          min={0}
          max={300}
          value={price[1]}
          onChange={(e) => setPrice([price[0], Number(e.target.value)])}
          className="w-full accent-accent"
        />
      </FilterGroup>
    </div>
  );

  return (
    <div className="container-nx py-8 md:py-12">
      <div className="flex items-end justify-between mb-8">
        <div>
          <h1 className="font-display text-3xl md:text-5xl font-black">Shop All</h1>
          <p className="text-muted-foreground mt-1 text-sm">{filtered.length} products</p>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => setOpen(true)}
            className="lg:hidden inline-flex items-center gap-2 border border-border px-4 py-2 rounded-full text-sm"
          >
            <SlidersHorizontal className="h-4 w-4" /> Filters
          </button>
          <select
            value={sort}
            onChange={(e) => setSort(e.target.value as typeof sort)}
            className="border border-border rounded-full px-4 py-2 text-sm bg-background"
          >
            {SORTS.map((s) => <option key={s.id} value={s.id}>{s.label}</option>)}
          </select>
        </div>
      </div>

      <div className="grid lg:grid-cols-[260px_1fr] gap-10">
        <aside className="hidden lg:block">{Filters}</aside>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-10">
          {filtered.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
        </div>
      </div>

      {open && (
        <div className="fixed inset-0 z-50 bg-black/50 lg:hidden" onClick={() => setOpen(false)}>
          <div className="absolute right-0 top-0 h-dvh w-[88%] max-w-sm bg-background p-6 overflow-y-auto" onClick={(e) => e.stopPropagation()}>
            <div className="flex justify-between items-center mb-6">
              <h3 className="font-display text-xl font-bold">Filters</h3>
              <button aria-label="Close filters" onClick={() => setOpen(false)} className="p-2"><X className="h-5 w-5" /></button>
            </div>
            {Filters}
          </div>
        </div>
      )}
    </div>
  );
}

function FilterGroup({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div>
      <h4 className="eyebrow mb-3">{title}</h4>
      <div className="space-y-2">{children}</div>
    </div>
  );
}

function Check({ label, checked, onChange }: { label: string; checked: boolean; onChange: () => void }) {
  return (
    <label className="flex items-center gap-2 text-sm cursor-pointer">
      <input type="checkbox" checked={checked} onChange={onChange} className="accent-accent" />
      {label}
    </label>
  );
}

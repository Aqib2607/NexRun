import { createFileRoute, Link, notFound } from "@tanstack/react-router";
import { categories, getByCategory } from "@/lib/mock-data";
import { ProductCard } from "@/components/products/ProductCard";

export const Route = createFileRoute("/category/$slug")({
  loader: ({ params }) => {
    const category = categories.find((c) => c.slug === params.slug);
    if (!category) throw notFound();
    return { category, products: getByCategory(params.slug) };
  },
  head: ({ loaderData }) => ({
    meta: [
      { title: `${loaderData?.category.name} — NexRun` },
      { name: "description", content: loaderData?.category.tagline },
      { property: "og:image", content: loaderData?.category.image },
    ],
  }),
  notFoundComponent: () => (
    <div className="container-nx py-24 text-center">
      <h1 className="font-display text-3xl font-bold">Category not found</h1>
      <Link to="/shop" className="mt-4 inline-block underline">Back to shop</Link>
    </div>
  ),
  component: CategoryPage,
});

function CategoryPage() {
  const { category, products } = Route.useLoaderData();
  return (
    <div>
      <section className="relative h-[50vh] min-h-[360px] bg-ink text-white overflow-hidden">
        <img src={category.image} alt="" className="absolute inset-0 h-full w-full object-cover opacity-70" />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent" />
        <div className="relative container-nx h-full flex items-end pb-12">
          <div>
            <span className="eyebrow text-accent">{category.tagline}</span>
            <h1 className="font-display text-5xl md:text-7xl font-black mt-2">{category.name}</h1>
          </div>
        </div>
      </section>

      <div className="container-nx py-10">
        <p className="text-sm text-muted-foreground mb-8">{products.length} products</p>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
          {products.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
        </div>
      </div>
    </div>
  );
}

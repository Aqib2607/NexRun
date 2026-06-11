import { createFileRoute } from "@tanstack/react-router";
import { products } from "@/lib/mock-data";

export const Route = createFileRoute("/admin/")({
  component: Dashboard,
});

function Dashboard() {
  const stats = [
    ["Revenue", "$24,580"],
    ["Orders", "156"],
    ["Customers", "1,204"],
    ["Products", String(products.length)],
  ];
  return (
    <div>
      <h1 className="font-display text-3xl font-black mb-6">Dashboard</h1>
      <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map(([l, v]) => (
          <div key={l} className="bg-surface rounded-xl p-5">
            <p className="eyebrow text-muted-foreground">{l}</p>
            <p className="font-display text-3xl font-black mt-2">{v}</p>
          </div>
        ))}
      </div>
      <div className="mt-8 bg-surface rounded-xl p-10 text-center text-muted-foreground">
        Charts, recent orders and activity feed go here.
      </div>
    </div>
  );
}

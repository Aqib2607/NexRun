import { createFileRoute } from "@tanstack/react-router";

const TITLES: Record<string, string> = {
  products: "Products", categories: "Categories", orders: "Orders",
  customers: "Customers", inventory: "Inventory", coupons: "Coupons",
  reviews: "Reviews", reports: "Reports", users: "Users", settings: "Settings",
};

export const Route = createFileRoute("/admin/$section")({
  component: () => {
    const { section } = Route.useParams();
    return (
      <div>
        <h1 className="font-display text-3xl font-black mb-6">{TITLES[section] ?? "Admin"}</h1>
        <div className="bg-surface rounded-xl p-12 text-center text-muted-foreground">
          Admin scaffold for {TITLES[section] ?? section}. Tables and forms can be plugged in here.
        </div>
      </div>
    );
  },
});

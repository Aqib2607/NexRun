import { createFileRoute, Link, Outlet, useLocation } from "@tanstack/react-router";
import { LayoutDashboard, Package, Tag, ShoppingBag, Users, Boxes, Ticket, Star, BarChart3, UserCog, Settings } from "lucide-react";

export const Route = createFileRoute("/admin")({
  head: () => ({ meta: [{ title: "Admin — NexRun" }] }),
  component: AdminLayout,
});

const NAV = [
  { to: "/admin", label: "Dashboard", icon: LayoutDashboard, exact: true },
  { to: "/admin/products", label: "Products", icon: Package },
  { to: "/admin/categories", label: "Categories", icon: Tag },
  { to: "/admin/orders", label: "Orders", icon: ShoppingBag },
  { to: "/admin/customers", label: "Customers", icon: Users },
  { to: "/admin/inventory", label: "Inventory", icon: Boxes },
  { to: "/admin/coupons", label: "Coupons", icon: Ticket },
  { to: "/admin/reviews", label: "Reviews", icon: Star },
  { to: "/admin/reports", label: "Reports", icon: BarChart3 },
  { to: "/admin/users", label: "Users", icon: UserCog },
  { to: "/admin/settings", label: "Settings", icon: Settings },
];

function AdminLayout() {
  const loc = useLocation();
  return (
    <div className="container-nx py-8 md:py-10">
      <div className="grid lg:grid-cols-[240px_1fr] gap-8">
        <aside>
          <div className="font-display text-xl font-black mb-4">Admin</div>
          <nav className="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible">
            {NAV.map((n) => {
              const active = n.exact ? loc.pathname === n.to : loc.pathname.startsWith(n.to);
              const Icon = n.icon;
              return (
                <Link key={n.to} to={n.to} className={`flex items-center gap-3 px-3 py-2 rounded-md text-sm shrink-0 ${active ? "bg-primary text-primary-foreground" : "hover:bg-muted"}`}>
                  <Icon className="h-4 w-4" /> {n.label}
                </Link>
              );
            })}
          </nav>
        </aside>
        <section><Outlet /></section>
      </div>
    </div>
  );
}

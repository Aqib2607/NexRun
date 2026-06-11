import { createFileRoute, Link, Outlet, useLocation } from "@tanstack/react-router";
import { useAuth } from "@/store/auth";
import { Package, Heart, MapPin, Bell, Gift, Award, User, Settings, LogOut } from "lucide-react";

export const Route = createFileRoute("/account")({
  head: () => ({ meta: [{ title: "Account — NexRun" }] }),
  component: AccountLayout,
});

const NAV = [
  { to: "/account", label: "Overview", icon: User, exact: true },
  { to: "/account/orders", label: "Orders", icon: Package },
  { to: "/account/addresses", label: "Addresses", icon: MapPin },
  { to: "/account/wishlist", label: "Wishlist", icon: Heart },
  { to: "/account/notifications", label: "Notifications", icon: Bell },
  { to: "/account/referral", label: "Referral", icon: Gift },
  { to: "/account/loyalty", label: "Loyalty", icon: Award },
  { to: "/account/profile", label: "Profile", icon: User },
  { to: "/account/settings", label: "Settings", icon: Settings },
];

function AccountLayout() {
  const user = useAuth((s) => s.user);
  const logout = useAuth((s) => s.logout);
  const location = useLocation();

  if (!user) {
    return (
      <div className="container-nx py-24 text-center">
        <h1 className="font-display text-3xl font-bold">Sign in to your account</h1>
        <Link to="/login" className="mt-6 inline-block bg-primary text-primary-foreground px-6 py-3 rounded-full font-semibold">
          Sign in
        </Link>
      </div>
    );
  }

  return (
    <div className="container-nx py-10 md:py-16">
      <div className="grid lg:grid-cols-[260px_1fr] gap-10">
        <aside>
          <div className="bg-surface rounded-xl p-5 mb-4">
            <div className="font-display text-lg font-bold">{user.name}</div>
            <div className="text-xs text-muted-foreground">{user.email}</div>
          </div>
          <nav className="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible">
            {NAV.map((n) => {
              const active = n.exact ? location.pathname === n.to : location.pathname.startsWith(n.to);
              const Icon = n.icon;
              return (
                <Link
                  key={n.to}
                  to={n.to}
                  className={`flex items-center gap-3 px-4 py-2.5 rounded-md text-sm shrink-0 ${active ? "bg-primary text-primary-foreground" : "hover:bg-muted"}`}
                >
                  <Icon className="h-4 w-4" /> {n.label}
                </Link>
              );
            })}
            <button onClick={logout} className="flex items-center gap-3 px-4 py-2.5 rounded-md text-sm text-destructive hover:bg-muted">
              <LogOut className="h-4 w-4" /> Sign out
            </button>
          </nav>
        </aside>
        <section><Outlet /></section>
      </div>
    </div>
  );
}

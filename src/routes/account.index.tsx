import { createFileRoute } from "@tanstack/react-router";
import { useAuth } from "@/store/auth";
import { useCart } from "@/store/cart";
import { useWishlist } from "@/store/wishlist";

export const Route = createFileRoute("/account/")({
  component: Overview,
});

function Overview() {
  const user = useAuth((s) => s.user);
  const cartCount = useCart((s) => s.items.length);
  const wishCount = useWishlist((s) => s.ids.length);
  return (
    <div>
      <h1 className="font-display text-3xl md:text-4xl font-black">Hi, {user?.name}</h1>
      <p className="text-muted-foreground mt-2">Here's a snapshot of your NexRun activity.</p>
      <div className="mt-8 grid sm:grid-cols-3 gap-4">
        <Stat label="Orders" value="0" />
        <Stat label="In Bag" value={String(cartCount)} />
        <Stat label="Wishlist" value={String(wishCount)} />
      </div>
      <div className="mt-10 bg-surface rounded-xl p-6">
        <h2 className="font-display text-xl font-bold">Loyalty</h2>
        <p className="text-sm text-muted-foreground mt-1">You're 250 points away from <strong className="text-foreground">NexRun Pro</strong>.</p>
        <div className="mt-4 h-2 bg-background rounded-full overflow-hidden">
          <div className="h-full bg-accent" style={{ width: "60%" }} />
        </div>
      </div>
    </div>
  );
}

function Stat({ label, value }: { label: string; value: string }) {
  return (
    <div className="bg-surface rounded-xl p-5">
      <p className="text-xs eyebrow text-muted-foreground">{label}</p>
      <p className="mt-2 font-display text-3xl font-black">{value}</p>
    </div>
  );
}

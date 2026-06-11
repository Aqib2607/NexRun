import { createFileRoute } from "@tanstack/react-router";

const TITLES: Record<string, string> = {
  addresses: "Saved Addresses",
  wishlist: "Wishlist",
  notifications: "Notifications",
  referral: "Refer a friend",
  loyalty: "Loyalty",
  profile: "Profile",
  settings: "Settings",
};

export const Route = createFileRoute("/account/$section")({
  component: () => {
    const { section } = Route.useParams();
    const title = TITLES[section] ?? "Account";
    return (
      <div>
        <h1 className="font-display text-3xl font-black mb-6">{title}</h1>
        <div className="bg-surface rounded-xl p-10 text-center text-muted-foreground">
          This section is part of the customer dashboard scaffold. UI ready for content.
        </div>
      </div>
    );
  },
});
